import { render, screen } from '@testing-library/vue'
import { expect, test, vi } from 'vitest'
import ScheduleGrid from '../ScheduleGrid.vue'
import { bookingApi } from '../../services/bookingApi'

vi.mock('../../services/bookingApi', () => ({
    bookingApi: {
        getBookings: vi.fn()
    }
}))

test('memvalidasi bahwa grid membedakan status slot yang tersedia dan penuh secara visual maupun interaktif', async () => {
    const mockSlots = [
        { time_slot: '23:59', status: 'available' },
        { time_slot: '09:00', status: 'booked' }
    ]

    vi.mocked(bookingApi.getBookings).mockResolvedValueOnce({ data: mockSlots })

    render(ScheduleGrid)

    await screen.findByText('23:59')

    const availableSlot = screen.getByRole('button', { name: /23:59/i }) as HTMLButtonElement
    const bookedSlot = screen.getByRole('button', { name: /09:00/i }) as HTMLButtonElement

    expect(availableSlot.disabled).toBe(false)
    expect(bookedSlot.disabled).toBe(true)
})
