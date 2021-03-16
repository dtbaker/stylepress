<?php

namespace StylePress;

defined( 'STYLEPRESS_VERSION' ) || exit;

$options = Backend::get_instance()->get_config();

?>

<div id="stylepress-react" data-config="<?php echo htmlspecialchars( json_encode( $options ), ENT_QUOTES, 'UTF-8' );?>"></div>
