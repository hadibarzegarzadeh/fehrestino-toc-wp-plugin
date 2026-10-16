<div class="fehrestino-toc fehrestino-glass">
    <div class="fehrestino-toc-header">
        <h3 class="fehrestino-toc-title">
            <svg style="width:24px;height:24px;vertical-align:middle;margin-left:5px" viewBox="0 0 24 24">
                <path fill="currentColor" d="M3,13H15V11H3M3,17H15V15H3M3,9H15V7H3M17,13H21V11H17M17,17H21V15H17M17,9H21V7H17Z" />
            </svg>
            <?php echo esc_html($heading_text); ?>
        </h3>
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
