<?php

namespace Blumewas\MlpAktion\Admin\Menu;

use Blumewas\MlpAktion\Admin\Util\Menu;

class MlpAktionMenu extends Menu
{

    protected int $position = 100;

    protected string $title = 'MLP Aktion';

    public function render()
    {
        include admin_asset("templates/admin_menu.php");
    }

}
