<?php
$this->renderPartial('/front/busca-orden', array(
));
?>
<!--? Preloader Start -->
<div id="preloader-active">
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-inner position-relative">
            <div class="preloader-circle"></div>
            <div class="preloader-img pere-text">
                <img src="<?php echo Yii::app()->request->baseUrl; ?>/assets/front/img/logo/loder.jpg" alt="">
            </div>
        </div>
    </div>
</div>
<!-- Preloader Start -->
<!--? slider Area Start-->
<div class="slider-area ">
    <div class="slider-active">
        <!-- Single Slider -->
        <div class="banner">
            <div class="slider">
                <ul>
                    <li>  <div class="single-slider slider-height d-flex align-items-start">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-9 col-lg-9">
                                        <div class="hero__caption">
                                        </div>
                                        <!--Hero form -->

                                        <!-- Hero Pera -->
                                        <div class="hero-pera">
                                            <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> </li>
                    <li>  <div class="single-slider slider-height2 d-flex align-items-baseline">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-9 col-lg-9">
                                        <div class="hero__caption">
                                        </div>

                                        <!-- Hero Pera -->
                                        <div class="hero-pera">
                                            <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> </li>
                    <li> <div class="single-slider slider-height3 d-flex align-items-baseline">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-9 col-lg-9">
                                        <div class="hero__caption">
                                        </div>

                                        <!-- Hero Pera -->
                                        <div class="hero-pera">
                                            <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> </li>
                    <li>   <div class="single-slider slider-height4 d-flex align-items-baseline">
                            <div class="container">
                                <div class="row">
                                    <div class="col-xl-9 col-lg-9">
                                        <div class="hero__caption">
                                        </div>

                                        <!-- Hero Pera -->
                                        <div class="hero-pera">
                                            <p></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> </li>
                </ul>
                <div class="dots"> <a href="javascript:void(0);" rel="0" class="cur"></a> <a href="javascript:void(0);" rel="1"></a> <a href="javascript:void(0);" rel="2"></a> <a href="javascript:void(0);" rel="3"></a> </div>
                <div class="arrow"> <a href="javascript:void(0);" class="btn-left">&lt;</a> <a href="javascript:void(0);" class="btn-right">&gt;</a> </div>
            </div>
        </div>
    </div>
</div>
<div class="our-info-area pt-70 pb-40">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form id="frm_busca_orden"  class="form-inline">
                    <?php echo CHtml::hiddenField('action', 'buscarOrden') ?>
                    <?php
                    echo CHtml::textField('codigo_orden_busqueda', '', array(
                        'placeholder' => t("Código de Orden"),
                        'required' => true
                    ))
                    ?>  
                    <button type="submit" class="blue-button medium rounded"><?php echo t("Rastrear") ?></button>
                </form>	
            </div>
        </div>
    </div>
</div>
<!-- slider Area End-->
<!--? our info Start -->
<div class="our-info-area pt-70 pb-40">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-info mb-30">
                    <div class="info-icon">
                        <span class="flaticon-support"></span>
                    </div>
                    <div class="info-caption">
                        <p>Llámenos</p>
                        <span>+ (593) 999979075</span>
                        <br/>
                        <span>02-2443178</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-info mb-30">
                    <div class="info-icon">
                        <span class="flaticon-clock"></span>
                    </div>
                    <div class="info-caption">
                        <p>Domingo CERRADO</p>
                        <span>Lun - Vie 8.00 - 18.00</span>
                        <br/>
                        <span>Sáb 8.00 - 15.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-info mb-30">
                    <div class="info-icon">
                        <span class="flaticon-place"></span>
                    </div>
                    <div class="info-caption">
                        <p>Av. De la Prensa n47-239 y Gonzalo Salazar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- our info End -->
<!--? Categories Area Start -->
<div class="categories-area section-padding30">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- Section Tittle -->
                <div class="section-tittle text-center mb-80">
                    <span>Nuestros Servicios</span>
                    <h2>Qué podemos hacer por ud</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-cat text-center mb-50">
                    <div class="cat-icon">
                        <span class="flaticon-shipped"></span>
                    </div>
                    <div class="cat-cap">
                        <h5><a href="#">Envío Xpress</a></h5>
                        <p>Envío Xpress (60 min. para zona urbana y 120 min. para valles)</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-cat text-center mb-50">
                    <div class="cat-icon">
                        <span class="flaticon-ship"></span>
                    </div>
                    <div class="cat-cap">
                        <h5><a href="#">Control y seguimiento</a></h5>
                        <p>Control y seguimiento de sus envíos en nuestra plataforma en línea, disponible las 24 horas.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-cat text-center mb-50">
                    <div class="cat-icon">
                        <span class="flaticon-plane"></span>
                    </div>
                    <div class="cat-cap">
                        <h5><a href="#">Asesoramiento personalizado</a></h5>
                        <p>Asesoramiento personalizado, en búsqueda de mejoras y satisfacción operacional de nuestros clientes.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-cat text-center mb-50">
                    <div class="cat-icon">
                        <span class="flaticon-place"></span>
                    </div>
                    <div class="cat-cap">
                        <h5><a href="#">Tecnología de punta</a></h5>
                        <p>Generación de guías desde la web, optimizando el tiempo para la recolección y entrega.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-cat text-center mb-50">
                    <div class="cat-icon">
                        <span class="flaticon-support"></span>
                    </div>
                    <div class="cat-cap">
                        <h5><a href="#">Guías</a></h5>
                        <p>Control y seguimiento de sus envíos en nuestra plataforma en línea, disponible las 24 horas.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="single-cat text-center mb-50">
                    <div class="cat-icon">
                        <span class="flaticon-clock"></span>
                    </div>
                    <div class="cat-cap">
                        <h5><a href="#">Personal</a></h5>
                        <p>Contamos con el personal 100% calificado y capacitado para el proceso de tus ordenes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Categories Area End -->
<!--? About Area Start -->
<div class="about-low-area padding-bottom">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="about-caption mb-50">
                    <!-- Section Tittle -->
                    <div class="section-tittle mb-35">
                        <span>Qué es Cargo Xpress?</span>
                        <h2></h2>
                    </div>
                    <p style="text-align:justify">Somos una empresa ecuatoriana especializada en el manejo profesional de envíos de sobres y paquetería liviana, con experiencia en el mercado. Garantizamos la satisfacción de nuestros clientes, cubriendo sus requerimientos, necesidades de recepción y entrega puerta a puerta de sus documentos y paquetes, con tecnología de punta y un talento humano competente, motivado y sobre todo comprometido con el desarrollo de tu empresa o emprendimiento.</p>

                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <!-- about-img -->
                <div class="about-img ">
                    <div class="about-font-img">
                        <img height="450" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/front/img/gallery/5.jpeg" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- About Area End -->
<!-- Contact Section Start -->
<section id="contacto" class="section padding-bottom">      
    <div class="contact-form">
        <div class="container">
            <div class="section-header">          
                <h2 class="section-title">CONTÁCTENOS</h2>
            </div>
            <div class="row">       
                <div class="col-lg-7 col-md-12 col-xs-12" style="
    padding-right: 0px;">
                    <div class="video_style text-center">
                        <video id="play" src="<?php Yii::app()->baseUrl ?>/assets/video/1.mp4" width="590" height="355"  preload="none" controls>
                        </video>
                    </div>   
                </div>
                <div class="col-lg-5 col-md-12 col-xs-12" style="
    padding-right: 0px;">
                    <div class="contact-block">
                        <form id='EnviaMail'  method="POST" onsubmit="return false;">
                            <?php echo CHtml::hiddenField('action', 'enviarFormularioContacto') ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <input type="text" class="form-control" id="name" name="name" placeholder="Nombre y Apellido" required data-error="Por favor ingrese nombre y apellido">
                                        <span style="position:absolute; right:21px;top:15px;" class="fa fa-user"></span>
                                        <div class="help-block with-errors"></div>
                                    </div>                                 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span style="position:absolute; right:21px;top:15px;" class="fa fa-envelope-o"></span>
                                        <input type="text" placeholder="Correo Electrónico" id="email" class="form-control" name="email" required data-error="Por favor ingrese su email">
                                        <div class="help-block with-errors"></div>
                                    </div> 
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <span style="position:absolute; right:21px;top:15px;" class="fa fa-mobile"></span>
                                        <input type="number" placeholder="Teléfono" id="telefono" class="form-control" name="telefono" required data-error="Por favor ingrese su teléfono">
                                        <div class="help-block with-errors"></div>
                                    </div> 
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group"> 
                                        <span style="position:absolute; right:21px;top:15px;" class="fa fa-comment-o"></span>
                                        <input type="text" class="form-control" id="mensaje"  name="mensaje"  placeholder="Mensaje"  data-error="Escriba su mensaje" required/>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="submit-button">
                                        <button class="btn btn-common btn-effect" id="submit" type="submit" name="action">Enviar</button>
                                        <div id="msgSubmit" class="h3 hidden"></div> 
                                        <div class="clearfix"></div> 
                                    </div>
                                </div>
                            </div>            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>            
</section>
<!-- Contact Section End -->
<?php
Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/assets/front/js/whatsappme.min.js', CClientScript::POS_END
);
Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/assets/banner/slider.js', CClientScript::POS_END
);
$baseUrl = Yii::app()->baseUrl . "";
$cs = Yii::app()->getClientScript();
$cs->registerCssFile($baseUrl . "/assets/front/css/whatsappme.min.css");
?>
