<?php
 
namespace App\Repositories\GroupTemplate;

use App\Repositories\Group\Group;
use App\Repositories\Template\Template;

/**
 * GroupTemplateInterface
 * 
 * Here we should have methods that are going to be specific  for this entity only
 */
interface GroupTemplateInterface
{
    public function deleteTemplateGroup(Template $template, Group $group);
}