@push('scripts')
    <script>
        var pauseLabelDownloader = 0;

        const downloadNextAvailableLabel = async () => {
            await axios({
                url: "{{ route('admin.courier-manager.shipments.pending-labels') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                },
                responseType: "blob",
            }).then((response) => {
                if (response.headers['content-disposition'] !== undefined) {
                    let filename = response.headers['content-disposition'].split('filename=')[1].split('.')[0].replace(/['"]+/g, '');
                    let extension = response.headers['content-disposition'].split('.')[1].split(';')[0].replace(/['"]+/g, '');
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", `${filename}.${extension}`);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                }
            }).catch((error) => {
                // Add 5 seconds after each fail
                pauseLabelDownloader = pauseLabelDownloader + 5000;
            });

            // Wait 1 second + the pause before getting more
            await new Promise(resolve => setTimeout(resolve, 1000 + pauseLabelDownloader));

            downloadNextAvailableLabel();
        }

        downloadNextAvailableLabel();
    </script>
@endpush
