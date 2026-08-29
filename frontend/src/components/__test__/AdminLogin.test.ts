import { render, fireEvent } from '@testing-library/vue'
import { describe, test, expect, vi, beforeEach } from 'vitest'
import AdminLogin from '../AdminLogin.vue'

// ===========================
// MOCK DEPENDENCIES
// ===========================

const mockPush = vi.fn()

vi.mock('vue-router', () => ({
    useRouter: () => ({ push: mockPush }),
}))

const { mockLogin } = vi.hoisted(() => ({
    mockLogin: vi.fn(),
}))

vi.mock('../../services/authApi', () => ({
    authApi: { login: mockLogin },
}))

// ===========================
// SETUP
// ===========================

beforeEach(() => {
    vi.clearAllMocks()
})

// ===========================
// TEST CASES
// ===========================

describe('AdminLogin', () => {
    test('merender form login dengan benar', () => {
        const { getByLabelText, getByRole } = render(AdminLogin)

        getByLabelText(/email/i)
        getByLabelText(/password/i)
        getByRole('button', { name: /masuk/i })
    })

    test('tombol submit disabled saat sedang loading', async () => {
        // bikin login pending supaya button stuck di loading state
        mockLogin.mockReturnValueOnce(new Promise(() => {}))

        const { getByRole, getByLabelText } = render(AdminLogin)

        await fireEvent.update(getByLabelText(/email/i), 'admin@test.com')
        await fireEvent.update(getByLabelText(/password/i), 'password123')
        await fireEvent.click(getByRole('button', { name: /masuk/i }))

        const button = getByRole('button', { name: /memproses/i }) as HTMLButtonElement
        expect(button.disabled).toBe(true)
    })

    test('menampilkan pesan error saat login gagal', async () => {
        mockLogin.mockResolvedValueOnce({
            success: false,
            message: 'Email atau password salah.',
        })

        const { getByRole, getByLabelText, findByText } = render(AdminLogin)

        await fireEvent.update(getByLabelText(/email/i), ' salah@test.com')
        await fireEvent.update(getByLabelText(/password/i), 'wrongpass')
        await fireEvent.click(getByRole('button', { name: /masuk/i }))

        const errorMsg = await findByText('Email atau password salah.')
        expect(errorMsg).toBeTruthy()
    })

    test('login sukses → navigasi ke dashboard admin', async () => {
        mockLogin.mockResolvedValueOnce({ success: true, data: { token: 'abc123' } })

        const { getByRole, getByLabelText } = render(AdminLogin)

        await fireEvent.update(getByLabelText(/email/i), 'admin@test.com')
        await fireEvent.update(getByLabelText(/password/i), 'password123')
        await fireEvent.click(getByRole('button', { name: /masuk/i }))

        // tunggu async selesai
        await vi.waitFor(() => {
            expect(mockPush).toHaveBeenCalledWith({ name: 'admin' })
        })
    })

    test('menampilkan error dari response 401/422', async () => {
        mockLogin.mockRejectedValueOnce({
            response: { data: { message: 'Kredensial tidak valid.' } },
        })

        const { getByRole, getByLabelText, findByText } = render(AdminLogin)

        await fireEvent.update(getByLabelText(/email/i), 'admin@test.com')
        await fireEvent.update(getByLabelText(/password/i), 'wrongpass')
        await fireEvent.click(getByRole('button', { name: /masuk/i }))

        const errorMsg = await findByText('Kredensial tidak valid.')
        expect(errorMsg).toBeTruthy()
    })
})
