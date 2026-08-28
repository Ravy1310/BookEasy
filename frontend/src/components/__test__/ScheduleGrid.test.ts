import { render } from '@testing-library/vue'
import { expect, test, vi } from 'vitest'
import ScheduleGrid from '../ScheduleGrid.vue'
import { bookingApi } from '../../services/bookingApi'

vi.mock('../../services/bookingApi', () => ({
    bookingApi: {
        getBookings: vi.fn()
    }
}))

test('memvalidasi bahwa grid membedakan status slot yang tersedia dan penuh secara visual maupun interaktif', async () => {
    // Ubah jam mock ke larut malam agar tidak otomatis terkunci oleh waktu lokal perangkat
    const mockSlots = [
        { time_slot: '23:00', status: 'available' },
        { time_slot: '23:30', status: 'booked' }
    ]

    vi.mocked(bookingApi.getBookings).mockResolvedValueOnce({ data: mockSlots })

    const { findByText, getByText } = render(ScheduleGrid)

    const availableSlot = await findByText('23:00') as HTMLButtonElement
    const bookedSlot = getByText('23:30') as HTMLButtonElement

    expect(availableSlot.disabled).toBe(false)
    expect(bookedSlot.disabled).toBe(true)
})