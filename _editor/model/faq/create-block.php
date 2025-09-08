<?php

/**
 * faq_createblocks
 *
 * @param $page
 * @return string
 */
function faq_createblocks($page) {

    $page->info = json_decode($page->info);

    $blocks = "";
    if (isset($page->info->savedata)) {
        foreach ($page->info->savedata as $key => $data) {
                $blocks .= "
                    <div class=\"faq-item\">
                        <input type=\"radio\" id=\"faq-{$key}\" name=\"faq\" class=\"faq-toggle\">
                        <label for=\"faq-{$key}\" class=\"faq-question\">
                            <span>{$data->title}</span>
                            <span class=\"arrow\"></span>
                        </label>
                        <div class=\"faq-answer\">
                            <p>{$data->description}</p>
                        </div>
                    </div>\n";
        }
    }

    return "<div>{$blocks}</div>";
}
