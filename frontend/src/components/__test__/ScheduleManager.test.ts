import { render, fireEvent } from '@testing-library/vue'
import { describe, test, expect, vi, beforeEach } from 'vitest'
import ScheduleManager from '../ScheduleManager.vue'

const { mockGetSchedules, mockUpdateSchedules } = vi.hoisted(() => ({
    mockGetSchedules: vi.fn(),
    mockUpdateSchedules: vi.fn(),
}))

vi.mock('../../services/adminApi', () => ({
    adminApi: {
        getSchedules: mockGetSchedules,
        updateSchedules: mockUpdateSchedules,
    },
}))

beforeEach(() => {
    vi.clearAllMocks()
    mockGetSchedules.mockResolvedValue({ data: [] })
})

describe('ScheduleManager', () => {
    test('merender 7 hari dengan jam default saat data kosong', async () => {
        const { findByText } = render(ScheduleManager)

        const hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']
        for (const nama of hari) {
            const el = await findByText(nama)
            expect(el).toBeTruthy()
        }
    })

    test('toggle tutup hari menonaktifkan input jam', async () => {
        const { getByRole, getByLabelText, findByText } = render(ScheduleManager)

        await findByText('Senin')

        // pakai role switch biar gak ambiguous sama input "jam tutup senin"
        const toggleSenin = getByRole('switch', { name: /tutup senin/i })
        await fireEvent.click(toggleSenin)

        const jamBuka = getByLabelText(/jam buka senin/i) as HTMLInputElement
        const jamTutup = getByLabelText(/jam tutup senin/i) as HTMLInputElement

        expect(jamBuka.disabled).toBe(true)
        expect(jamTutup.disabled).toBe(true)
    })

    test('validasi jam tutup harus lebih dari jam buka', async () => {
        // data invalid dari backend: jam buka 17:00, jam tutup 09:00
        mockGetSchedules.mockResolvedValueOnce({
            data: [
                { day_of_week: 0, start_time: '17:00', end_time: '09:00', is_closed: false },
            ],
        })

        const { findByText, getByRole } = render(ScheduleManager)
        await findByText('Senin')

        const saveBtn = getByRole('button', { name: /simpan perubahan/i })
        await fireEvent.click(saveBtn)

        const error = await findByText(/jam tutup harus lebih dari jam buka/i)
        expect(error).toBeTruthy()
    })

    test('simpan jadwal berhasil menampilkan toast sukses', async () => {
        mockUpdateSchedules.mockResolvedValueOnce({ success: true })

        const { getByRole, findByText } = render(ScheduleManager)
        await findByText('Senin')

        const saveBtn = getByRole('button', { name: /simpan perubahan/i })
        await fireEvent.click(saveBtn)

        const toast = await findByText(/jadwal berhasil diperbarui/i)
        expect(toast).toBeTruthy()
        expect(mockUpdateSchedules).toHaveBeenCalled()
    })

    test('loading skeleton muncul saat fetch data', () => {
        mockGetSchedules.mockReturnValueOnce(new Promise(() => {}))

        const { container } = render(ScheduleManager)

        const skeletons = container.querySelectorAll('.animate-pulse')
        expect(skeletons.length).toBeGreaterThan(0)
    })
})
