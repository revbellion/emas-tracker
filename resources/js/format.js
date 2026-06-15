export function formatDatetime(value) {
    if (!value) return '';
    const d = new Date(value);
    return d.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
