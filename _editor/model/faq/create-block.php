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
        foreach ($page->info->savedata as $data) {

                $blocks .= "
                    <div class=\"faq-editor-item border-b border-gray-200 pb-3\"
                        id=\"faq-editor-item-1\">
                        <h3 class=\"w-100 text-left d-flex justify-content-between align-items-center\">
                          <span>{$data->title}</span>
                            <svg fill=\"none\" stroke=\"currentColor\" height=\"24\" width=\"24\" viewBox=\"0 0 24 24\">
                                <path stroke-linecap=\"round\"
                                      stroke-linejoin=\"round\"
                                      stroke-width=\"2\"
                                      d=\"M19 9l-7 7-7-7\"></path>
                            </svg>
                        </h3>
                        <div class=\"mt-2 faq-answer faq-hidden\">
                            <p>{$data->description}</p>
                        </div>
                    </div>\n";
        }
    }

    return "<div>{$blocks}</div>";
}
