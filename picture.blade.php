{{-- resources/views/components/imager.blade.php --}}
@props([
    'src',
    'width' => 600,
    'height' => 400,
    'quality' => 30,
    'focalX' => 50,
    'focalY' => 50,
    'format' => 'webp',
])

@php
  $upload_dir = wp_get_upload_dir();
  $site_name = get_bloginfo('name');
  $default_image_url = 'https://placehold.co/600x400'; // Default placeholder image URL

  if (empty($src)) {
      $image_url = $default_image_url;
      $alt = $title = $ariaLabel = "Platzhalterbild";
  } else {
      if (is_numeric($src)) {
          $image_url = wp_get_attachment_url($src);
      } elseif (filter_var($src, FILTER_VALIDATE_URL)) {
          $image_url = $src;
      } elseif (function_exists('get_field') && $acf_image = get_field($src)) {
          $image_url = is_array($acf_image) ? $acf_image['url'] : $acf_image;
      } else {
          $image_url = $upload_dir['baseurl'] . '/' . ltrim($src, '/');
      }

      if (empty($image_url)) {
          $image_url = $default_image_url;
          $alt = $title = $ariaLabel = "Platzhalterbild";
      } else {
          $cache_key = 'optimized_image_' . md5($image_url . $width . $height . $quality . $format);
          $optimized_data = get_transient($cache_key);

          if (!$optimized_data) {
              $attachment_id = attachment_url_to_postid($image_url);
              $alt = $attachment_id ? get_post_meta($attachment_id, '_wp_attachment_image_alt', true) : $site_name;
              $title = $attachment_id ? get_the_title($attachment_id) : $site_name;
              $ariaLabel = $alt;

              $filename = pathinfo(parse_url($image_url, PHP_URL_PATH), PATHINFO_FILENAME);
              $optimized_path = $upload_dir['basedir'] . '/optimized-' . $filename . '-' . $width . 'x' . $height . '.' . $format;
              $optimized_url = $upload_dir['baseurl'] . '/optimized-' . $filename . '-' . $width . 'x' . $height . '.' . $format;

              if (!file_exists($optimized_path)) {
                  $source_path = filter_var($image_url, FILTER_VALIDATE_URL) && strpos($image_url, $upload_dir['baseurl']) === false
                      ? sys_get_temp_dir() . '/' . basename($image_url)
                      : $upload_dir['basedir'] . '/' . ltrim(str_replace($upload_dir['baseurl'], '', $image_url), '/');

                  if (!file_exists($source_path) && filter_var($image_url, FILTER_VALIDATE_URL)) {
                      copy($image_url, $source_path);
                  }

                  \Spatie\Image\Image::load($source_path)
                      ->width($width)
                      ->height($height)
                      ->quality($quality)
                      ->format($format)
                      ->focalCrop($width, $height, $focalX, $focalY)
                      ->save($optimized_path);
              }

              set_transient($cache_key, [
                  'optimized_url' => $optimized_url,
                  'alt' => $alt,
                  'title' => $title,
                  'ariaLabel' => $ariaLabel,
              ], DAY_IN_SECONDS);
          } else {
              $optimized_url = $optimized_data['optimized_url'];
              $alt = $optimized_data['alt'];
              $title = $optimized_data['title'];
              $ariaLabel = $optimized_data['ariaLabel'];
          }
      }
  }
@endphp

<img
  src="{{ esc_url($optimized_url ?? $default_image_url) }}"
  alt="{{ $alt }}"
  title="{{ $title }}"
  width="{{ $width }}"
  height="{{ $height }}"
  decoding="async"
  role="img"
  itemprop="image"
  aria-label="Image {{ $ariaLabel }}"
  {{ $attributes }}
/>
