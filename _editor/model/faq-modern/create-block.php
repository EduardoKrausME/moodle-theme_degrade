<?php

/**
 * faq_createblocks
 *
 * @param $page
 * @return string
 */
function faq_modern_createblocks($page) {

    $page->info = json_decode($page->info);

    $blocks = "";
    if (isset($page->info->savedata)) {
        foreach ($page->info->savedata as $key => $data) {
                $blocks .= "
                    <div class=\"faq-item\">
                        <input type=\"radio\" name=\"faq-modern\" id=\"faq-modern-{$key}\">
                        <label for=\"faq-modern-{$key}\" class=\"faq-question\">{$data->title}</label>
                        <div class=\"faq-answer\">
                            <p>{$data->description}</p>
                        </div>
                    </div>\n";
        }
    }

    return "<div>{$blocks}</div>";
}
