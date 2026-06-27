import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

/**
 * Composable for SweetAlert2 with predefined configurations
 * Provides consistent alert styling and behavior across the application
 */
export function useSweetAlert() {
  /**
   * Show a success alert
   * @param {string} text - The message to display
   * @param {string} title - The title of the alert (default: 'Berhasil')
   * @param {number} timer - Auto-close timer in ms (default: 1500)
   */
  const success = (text, title = 'Berhasil', timer = 1500) => {
    return Swal.fire({
      title,
      text,
      icon: 'success',
      timer,
      showConfirmButton: false,
    });
  };

  /**
   * Show an error alert
   * @param {string} text - The error message to display
   * @param {string} title - The title of the alert (default: 'Gagal')
   * @param {number} timer - Auto-close timer in ms (default: 3000)
   */
  const error = (text, title = 'Gagal', timer = 3000) => {
    return Swal.fire({
      title,
      text,
      icon: 'error',
      timer,
      showConfirmButton: true,
    });
  };

  /**
   * Show a validation error alert
   * @param {string} text - The validation message (default: 'Mohon periksa kembali data yang dimasukkan')
   */
  const validationError = (text = 'Mohon periksa kembali data yang dimasukkan') => {
    return error(text, 'Error');
  };

  /**
   * Show a warning alert
   * @param {string} text - The warning message to display
   * @param {string} title - The title of the alert (default: 'Peringatan')
   */
  const warning = (text, title = 'Peringatan') => {
    return Swal.fire({
      title,
      text,
      icon: 'warning',
      showConfirmButton: true,
    });
  };

  /**
   * Show an info alert
   * @param {string} text - The info message to display
   * @param {string} title - The title of the alert (default: 'Informasi')
   */
  const info = (text, title = 'Informasi') => {
    return Swal.fire({
      title,
      text,
      icon: 'info',
      showConfirmButton: true,
    });
  };

  /**
   * Show a confirmation dialog
   * @param {string} text - The confirmation message
   * @param {string} title - The title of the dialog (default: 'Apakah Anda yakin?')
   * @param {string} confirmButtonText - Text for confirm button (default: 'Ya')
   * @param {string} cancelButtonText - Text for cancel button (default: 'Batal')
   * @returns {Promise<boolean>} - Returns true if confirmed, false if cancelled
   */
  const confirm = async (
    text,
    title = 'Apakah Anda yakin?',
    confirmButtonText = 'Ya',
    cancelButtonText = 'Batal'
  ) => {
    const result = await Swal.fire({
      title,
      text,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText,
      cancelButtonText,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
    });
    return result.isConfirmed;
  };

  /**
   * Show a delete confirmation dialog
   * @param {string} text - The confirmation message (default: 'Data yang dihapus tidak dapat dikembalikan!')
   * @returns {Promise<boolean>} - Returns true if confirmed, false if cancelled
   */
  const confirmDelete = async (text = 'Data yang dihapus tidak dapat dikembalikan!') => {
    const result = await Swal.fire({
      title: 'Apakah Anda yakin?',
      text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus!',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
    });
    return result.isConfirmed;
  };

  /**
   * Show a loading alert
   * @param {string} text - The loading message (default: 'Mohon tunggu...')
   */
  const loading = (text = 'Mohon tunggu...') => {
    return Swal.fire({
      title: text,
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
  };

  /**
   * Close the current Swal alert
   */
  const close = () => {
    Swal.close();
  };

  /**
   * Custom alert with full control over options
   * @param {object} options - SweetAlert2 options object
   */
  const custom = (options) => {
    return Swal.fire(options);
  };

  return {
    success,
    error,
    validationError,
    warning,
    info,
    confirm,
    confirmDelete,
    loading,
    close,
    custom,
  };
}
