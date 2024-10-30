### Project: <x-picture> Component for Optimized Image Handling in WordPress (Sage-based)

The <x-picture> component provides a refined and efficient solution for image handling in WordPress projects based on the Roots.io Sage framework. It minimizes reliance on default WordPress media processing, focusing instead on custom-optimized image rendering and management. This component bypasses WordPress’s default media handling and enables full control over image optimization, format, dimensions, and focal cropping—ideal for developers aiming for performance-driven, streamlined WordPress solutions.

## Why <x-picture>?

WordPress’s built-in media processing generates multiple sizes for each upload, which often leads to redundant image files and storage inefficiencies. By switching to <x-picture>, you gain:

- Custom, lean image generation with no redundant WordPress-generated versions.
- High-quality, adaptive image optimization with flexible parameters for precise control.
- Automatic cleanup of all optimized image versions when the original is deleted.
- Enhanced SEO and accessibility with custom alt, title, and aria-label attributes.
- Native compatibility with Sage to ensure a modern and developer-friendly integration.

## Key Features
- Customized Image Generation (components/picture.blade.php):
- Create responsive, optimized images directly within Blade templates.
- Configure dimensions, quality, format (WebP, JPG), and focal cropping as needed.
- Automate optimized image caching for quick re-use.
- Image Cleanup Function in functions.php:
- Automatically removes all optimized versions of an image upon deletion of the original.
- Simplifies storage management by removing unused files, ensuring minimal footprint.

## Advantages
- Full Control: Override WordPress’s default media handling to generate only the images you need.
- Customizable Quality and Formats: Configure dimensions, format, and quality without bloating storage.
- Automatic Cleanup: Optimized image versions are deleted alongside the original image, keeping storage tidy.
- SEO and Accessibility: Set alt, title, and aria-label attributes for each image instance.

Seamless Integration with Sage: Works natively with Roots.io’s Sage theme, providing modern image handling that aligns with Sage’s philosophy.

Switching to <x-picture> replaces WordPress’s default, size-intensive media handling with a focused, optimized image workflow. 
This approach ensures your images are responsive, SEO-friendly, and streamlined for a more efficient and effective WordPress experience.

## Installation and Setup
##### Add Spatie Dependencies to Your Theme Folder:
composer require spatie/image
composer require spatie/image-optimizer

##### Add the Custom Cleanup Function to functions.php:
This function ensures that optimized images are automatically deleted when the original is removed. Place this code in your functions.php file:

```
add_filter('intermediate_image_sizes_advanced', function($sizes) {
    return []; // Disables automatic generation of WordPress intermediate sizes
});

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
                    unlink($file);
                }
            }
        }
    }
});
```

##### Add picture.blade.php to Your Theme:
Place picture.blade.php in resources/views/components/ within your theme directory.


##### Usage Examples

##### WordPress Thumbnail (Featured Image)
To use <x-picture> with a featured image, pass in the thumbnail ID

```
<x-picture 
    :src="get_post_thumbnail_id()" 
    width="800" 
    height="600" 
    quality="80" 
    format="jpg" 
    alt="Featured Image"
/>
```

##### ACF Image Field
If using ACF to manage images, simply pass in the field name:
```
<x-picture 
    :src="'my_acf_image_field'"  {{-- Replace with your ACF field name --}}
    width="400" 
    height="300" 
    quality="70" 
    format="webp" 
    alt="Custom ACF Image"
/>
```
##### Static Image
For static images hosted on your site or a CDN, provide the full URL:
```
<x-picture 
    src="https://yourwebsite.com/wp-content/uploads/2024/10/static-image.jpg" 
    width="600" 
    height="400" 
    quality="90" 
    format="png" 
    alt="Static Image"
/>
```
