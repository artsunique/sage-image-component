
add_action('delete_attachment', function($attachment_id) {
    $upload_dir = wp_get_upload_dir();
    $original_path = get_attached_file($attachment_id);

    if ($original_path) {
        $filename = pathinfo($original_path, PATHINFO_FILENAME);
        $patterns = [
            $upload_dir['basedir'] . '/' . $filename . '-*',
            $upload_dir['basedir'] . '/optimized-' . $filename . '-*'
        ];

        foreach ($patterns as $pattern) {
            foreach (glob($pattern) as $file) {
                if (file_exists($file)) {
                    unlink($file); // Lösche die Datei
                }
            }
        }
    }
});
