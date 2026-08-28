import { render, fireEvent } from '@testing-library/vue'
import BookingForm from '../BookingForm.vue'
import { expect, test } from 'vitest'

test('memvalidasi bahwa tombol submit tetap ter-disable selama form belum lengkap', async () => {
    // Render form dengan state awal tanpa slot yang terpilih
    const { getByRole, getByLabelText } = render(BookingForm, {
        props: { selectedSlot: null, isSubmitting: false }
    })

    const button = getByRole('button', { name: /pesan sekarang/i }) as HTMLButtonElement
    expect(button.disabled).toBe(true)

    // Menguji interaksi saat user hanya mengisi teks tanpa mengklik jam di grid
    await fireEvent.update(getByLabelText(/nama pelanggan/i), 'Budi')
    await fireEvent.update(getByLabelText(/nomor whatsapp/i), '0812345678')

    // Memastikan state validasi tetap mengunci tombol eksekusi
    expect(button.disabled).toBe(true)
})

test('mengaktifkan tombol submit secara reaktif ketika seluruh syarat validasi terpenuhi', async () => {
    // Merender komponen dengan simulasi slot jam 10:00 yang sudah diklik
    const { getByRole, getByLabelText } = render(BookingForm, {
        props: { selectedSlot: '10:00', isSubmitting: false }
    })

    const button = getByRole('button', { name: /pesan sekarang/i }) as HTMLButtonElement

    await fireEvent.update(getByLabelText(/nama pelanggan/i), 'Budi')
    await fireEvent.update(getByLabelText(/nomor whatsapp/i), '0812345678')

    // Mengonfirmasi bahwa reaktivitas form berhasil membuka kunci tombol 
    expect(button.disabled).toBe(false)
})