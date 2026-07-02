<?php
defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Default card -->
<div class="card">

  <div class="card-header with-border">
    <h3 class="card-title"><?php echo lang('settings') ?></h3>
  </div>
  <ul class="list-group">



    <?php if (hasPermissions('general_settings')): ?>
      <a class="list-group-item list-group-item-action <?php echo ($page->submenu == 'general') ? 'active' : '' ?>" href="<?php echo url('settings/general') ?>"><?php echo lang('general_setings') ?></a>
    <?php endif ?>

    <?php if (hasPermissions('general_settings')): ?>
      <a class="list-group-item list-group-item-action <?php echo ($page->submenu == 'api_settings') ? 'active' : '' ?>" href="<?php echo url('settings/api_settings') ?>">Integrasi API</a>
    <?php endif ?>

    <?php if (hasPermissions('general_settings')): ?>
      <a class="list-group-item list-group-item-action <?php echo ($page->submenu == 'feature_settings') ? 'active' : '' ?>" href="<?php echo url('settings/feature_settings') ?>">Pengaturan Fitur</a>
    <?php endif ?>

    <?php if (hasPermissions('company_settings')): ?>
      <a class="list-group-item list-group-item-action <?php echo ($page->submenu == 'company') ? 'active' : '' ?>" href="<?php echo url('settings/company') ?>">Pengaturan Aplikasi</a>
    <?php endif ?>

    <?php if (hasPermissions('email_templates')): ?>
      <a class="list-group-item list-group-item-action <?php echo ($page->submenu == 'email_templates') ? 'active' : '' ?>" href="<?php echo url('settings/email_templates') ?>"><?php echo lang('email_templates') ?></a>
    <?php endif ?>

  </ul>

</div>