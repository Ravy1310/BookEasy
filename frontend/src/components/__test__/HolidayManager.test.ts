import { render, fireEvent, screen } from '@testing-library/vue'
import { describe, test, expect, vi, beforeEach } from 'vitest'
import HolidayManager from '../HolidayManager.vue'

const { mockGetHolidays, mockAddHoliday, mockRemoveHoliday } = vi.hoisted(() => ({
    mockGetHolidays: vi.fn(),
    mockAddHoliday: vi.fn(),
    mockRemoveHoliday: vi.fn(),
}))

vi.mock('../../services/adminApi', () => ({
    adminApi: {
        getHolidays: mockGetHolidays,
        addHoliday: mockAddHoliday,
        removeHoliday: mockRemoveHoliday,
    },
}))

beforeEach(() => {
    vi.clearAllMocks()
    mockGetHolidays.mockResolvedValue({ data: [] })
})

describe('HolidayManager', () => {
    test('menampilkan empty state saat tidak ada hari libur', async () => {
        const { findByText } = render(HolidayManager)

        const emptyMsg = await findByText(/tidak ada hari libur/i)
        expect(emptyMsg).toBeTruthy()
    })

    test('submit form tanpa tanggal menampilkan error', async () => {
        render(HolidayManager)

        await vi.waitFor(() => {
            expect(mockGetHolidays).toHaveBeenCalled()
        })

        // submit form langsung, bypass validasi browser required
        const form = screen.getByRole('button', { name: /tambah libur/i }).closest('form')!
        await fireEvent.submit(form)

        const error = await screen.findByText(/tanggal libur wajib diisi/i)
        expect(error).toBeTruthy()
    })

    test('tambah hari libur berhasil → re-fetch & reset form', async () => {
        mockAddHoliday.mockResolvedValueOnce({ success: true })

        render(HolidayManager)

        await vi.waitFor(() => {
            expect(mockGetHolidays).toHaveBeenCalled()
        })

        await fireEvent.update(screen.getByLabelText(/tanggal libur/i), '2026-12-25')
        await fireEvent.update(screen.getByLabelText(/alasan libur/i), 'Natal')

        const form = screen.getByRole('button', { name: /tambah libur/i }).closest('form')!
        await fireEvent.submit(form)

        await vi.waitFor(() => {
            expect(mockAddHoliday).toHaveBeenCalledWith('2026-12-25', 'Natal')
        })

        await vi.waitFor(() => {
            expect(mockGetHolidays).toHaveBeenCalledTimes(2)
        })

        const toast = await screen.findByText(/berhasil ditambahkan/i)
        expect(toast).toBeTruthy()
    })

    test('hapus hari libur dengan konfirmasi user', async () => {
        mockGetHolidays.mockResolvedValueOnce({
            data: [{ date: '2026-12-25', reason: 'Natal' }],
        })
        mockRemoveHoliday.mockResolvedValueOnce({ success: true })

        render(HolidayManager)

        await screen.findByText('Natal')

        const deleteBtn = screen.getByRole('button', { name: /hapus libur tanggal/i })
        await fireEvent.click(deleteBtn)

        // Modal muncul — klik tombol konfirmasi "Ya, Hapus"
        const confirmBtn = await screen.findByRole('button', { name: /ya, hapus/i })
        await fireEvent.click(confirmBtn)

        await vi.waitFor(() => {
            expect(mockRemoveHoliday).toHaveBeenCalledWith('2026-12-25')
        })
    })

    test('batal hapus saat user menolak konfirmasi', async () => {
        mockGetHolidays.mockResolvedValueOnce({
            data: [{ date: '2026-12-25', reason: 'Natal' }],
        })

        render(HolidayManager)

        await screen.findByText('Natal')

        const deleteBtn = screen.getByRole('button', { name: /hapus libur tanggal/i })
        await fireEvent.click(deleteBtn)

        // Modal muncul — klik tombol batal
        const cancelBtn = await screen.findByRole('button', { name: /^batal$/i })
        await fireEvent.click(cancelBtn)

        expect(mockRemoveHoliday).not.toHaveBeenCalled()
    })
})
