<?php

function putVars(array $vars)
{
    foreach ($vars as $key => $value) {
        echo sprintf(
            "--side-menu-%s: %s;\n",
            $key,
            'opener' === $key
                ? sprintf('url("%s")', image_path('side_menu', $value.'.svg'))
                : $value
        );
    }
}
?>

:root {
    <?php putVars($_['vars']['dark']); ?>
}

@media (prefers-color-scheme: light) {
  :root {
    <?php putVars($_['vars']['light']); ?>
  }
}

@media (prefers-color-scheme: dark) {
  <?php putVars($_['vars']['dark']); ?>
}

body[data-theme-dark], body[data-theme-dark-highcontrast] {
  <?php putVars($_['vars']['dark']); ?>
}

body[data-theme-light], body[data-theme-light-highcontrast] {
  <?php putVars($_['vars']['light']); ?>
}

<?php if ($_['opener-only']) { ?>
  #nextcloud {
    display: none;
  }
<?php } ?>

<?php if ('hidden' === $_['size-text']) { ?>
  .cm-apps {
    <?php if ('big' === $_['size-icon']) { ?>
      width: 55px;
    <?php } else { ?>
      width: 52px;
    <?php } ?>
  }

  .cm .cm-opener {
    <?php if ('big' === $_['size-icon']) { ?>
      margin-left: 1px;
    <?php } else { ?>
      margin-left: 0px;
    <?php } ?>
  }
<?php } ?>

<?php if ('hidden' === $_['size-icon']) { ?>
  .cm-app-icon {
    display: none;
  }
<?php } elseif ('small' === $_['size-icon']) { ?>
  .cm-app-icon svg {
    width: 15px;
    height: 15px;
  }

  img.cm-app-icon {
    width: 15px;
    height: 15px;
  }

  .cm-app a {
    padding-left: 16px !important;
  }
<?php } elseif ('normal' === $_['size-icon']) { ?>
  .cm-app-icon svg {
    width: 20px;
    height: 20px;
  }

  img.cm-app-icon {
    width: 20px;
    height: 20px;
  }
<?php } elseif ('big' === $_['size-icon']) { ?>
  .cm-app-icon svg {
    width: 23px;
    height: 23px;
  }

  img.cm-app-icon {
    width: 23px;
    height: 23px;
  }

  .cm-app a {
    padding-left: 11px !important;
  }
<?php } ?>

<?php if ('hidden' === $_['size-text']) { ?>
  .cm-app-text {
    display: none;
  }
<?php } elseif ('small' === $_['size-text']) { ?>
  .cm-app-text {
    font-size: 12px;
  }
<?php } elseif ('big' === $_['size-text']) { ?>
  .cm-app-text {
    font-size: 16px;
  }
<?php } ?>

<?php if ($_['always-displayed']) { ?>
  #content {
    left: 53px;
    width: calc(100% - (var(--body-container-margin) * 2) - 62px);
  }

  #content-vue {
    width: calc(100% - (var(--body-container-margin) * 2) - 60px);
    margin-left: 11px;
  }
<?php } ?>
