@push('scripts')
    <script>
        var labelDownloaderStop = false;

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
                let filename = response.headers['content-disposition'].split('filename=')[1].split('.')[0];
                let extension = response.headers['content-disposition'].split('.')[1].split(';')[0];

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", `${filename}.${extension}`);
                document.body.appendChild(link);
                link.click();
                link.remove();
            }).catch((error) => {
                labelDownloaderStop = true;
            });

            // Wait 3 seconds before getting more
            await new Promise(resolve => setTimeout(resolve, 3000));

            if (!labelDownloaderStop) {
                downloadNextAvailableLabel();
            }
        }

        downloadNextAvailableLabel();
    </script>
@endpush
