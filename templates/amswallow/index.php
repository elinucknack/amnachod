<?php

  defined("_JEXEC") or die;

  use Joomla\CMS\Factory;
  use Joomla\CMS\Uri\Uri;
  use Joomla\CMS\HTML\HTMLHelper;
  
  $this->addHeadLink(HTMLHelper::_('image', 'logo.svg', '', [], true, 1), 'icon', 'rel', ['type' => 'image/svg+xml']);

  $app = Factory::getApplication();
  $doc = $app->getDocument();
  $root = Uri::root();
  $lang = Factory::getLanguage();
  
  $template = $this->template;
  $sitename = $app->get("sitename");
  $title = $doc->getTitle();

  $wa = $this->getWebAssetManager();
  $wa->registerAndUseStyle('template-style', 'templates/' . $template . '/css/style.css');
  $wa->registerAndUseScript('template-script', 'templates/' . $template . '/js/main.js');

  $doc->setTitle($title == "Home" ? $sitename : $sitename . " - " . $title);
  
?>
<!DOCTYPE html>
<html>
  <head>
    <jdoc:include type="head" />
  </head>
  <body>
    <div id="header">
      <table>
        <tbody>
          <tr>
            <td id="logo">
              <img src="templates/amswallow/images/logo.svg" alt="logo.svg" onclick="location='<?php echo $root; ?>'" />
            </td>
            <td id="link-menu">
              <jdoc:include type="modules" name="odkazy" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>  
    <div id="top">  
      <table>
        <tbody>
          <tr>
            <td id="top-menu">
              <jdoc:include type="modules" name="obory" />
            </td>
            <td id="languages-menu">
              <jdoc:include type="modules" name="jazyky" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <table id="main">
      <tbody>
        <tr>
          <td id="content">
            <table>
              <tr>
                <td id="look">
                  <img id="irop-logo" src="templates/amswallow/images/irop-logo.png" alt="irop-logo.png" />
                  <h2><?php echo $lang->getTag() == "cs-CZ"? "Novinky": "Look"; ?></h2>
                  <jdoc:include type="modules" name="novinky" />
                </td>
                <td id="slideshow">
                  <jdoc:include type="modules" name="aktuality" />
                </td>
              </tr>
            </table>
            <div id="article">
              <jdoc:include type="component" />
            </div>
          </td>
          <td id="right">
            <div id="news">
              <h2><?php echo $lang->getTag() == "cs-CZ"? "Aktuality - úřední deska": "News"; ?></h2>
              <jdoc:include type="modules" name="mini-aktuality" />
            </div>
            <div id="right-menu">
              <jdoc:include type="modules" name="prave-menu" />
            </div>
            <div id="search">
              <jdoc:include type="modules" name="vyhledavani" />
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <div id="footer">
	  <img id="partners" src="templates/amswallow/images/partneri.png" alt="partneri.png" />
      <table>
        <tbody>
          <tr>
            <td id="icon">
              <img src="templates/amswallow/images/logo.svg" alt="logo.svg">
            </td>
            <td id="info">
              <jdoc:include type="modules" name="zapati" />
            </td>
            <td id="login">
              <p>
                <a href="administrator" target="_blank">LOGIN</a>
              </p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </body>
</html>
