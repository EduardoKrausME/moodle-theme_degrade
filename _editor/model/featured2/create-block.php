<?php

/**
 * featured2_createblocks
 *
 * @param $page
 * @return string
 */
function featured2_createblocks($page) {

    $page->info = json_decode($page->info);

    $blocks = "";
    if (isset($page->info->savedata)) {
        $num = 1;
        foreach ($page->info->savedata as $data) {
            $blocks .= "
                    <div class=\"step-item\">
                        <div class=\"step-icon\">
                            <span>{$num}</span>
                        </div>
                        <div class=\"step-content\">
                            <h3>{$data->title}</h3>
                            <p>{$data->description}</p>
                        </div>
                    </div>\n";
            $num++;
        }
    }

    return "<div>{$blocks}</div>";
}
