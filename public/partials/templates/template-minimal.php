<div class="fehrestino-toc fehrestino-minimal">
    <div class="fehrestino-toc-header">
        <h3 class="fehrestino-toc-title"><?php echo esc_html($heading_text); ?></h3>
        <button class="fehrestino-toggle">[مخفی کردن]</button>
    </div>
    <div class="fehrestino-content">
        <ul>
            <?php foreach ($toc_items as $item) : ?>
                <li class="fehrestino-level-<?php echo esc_attr($item['level']); ?>">
                    <a href="#<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['text']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
