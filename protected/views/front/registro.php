
<div class="our-info-area pt-70 pb-40">
    <div class="container"> 
        <h2 class="text-center">Registro</h2>


        <form id="frm" method="POST" onsubmit="return false;">
            <?php echo CHtml::hiddenField('action', 'signup') ?>


            <div class="row">

                <div class="col m6">
                    <div class="input-form">
                        <?php
                        echo CHtml::textField('nombre', ''
                                , array('class' => "validate",
                            'data-validation' => "required",
                            'Placeholder' => t("Nombre")
                        ))
                        ?>
                    </div>
                </div> <!--col-->

                <div class="col m6">
                    <div class="input-form">
                        <?php
                        echo CHtml::textField('apellido', ''
                                , array('class' => "validate",
                            'data-validation' => "required",
                            'Placeholder' => t("Apellido")
                        ))
                        ?>
                    </div>
                </div> <!--col-->

            </div> <!--row-->

            <div class="row">
                <div class="col m6">   
                    <div class="input-form">
                        <?php
                        echo CHtml::textField('telefono', ''
                                , array(
                            'class' => "validate mobile_inputs",
                            'data-validation' => "required",
                            'Placeholder' => t("Celular")
                        ))
                        ?>
                       <!--<label for="mobile_number"><?php echo t("Celular") ?></label>-->
                    </div>   
                </div>

                <div class="col m6">
                    <div class="input-form">
                        <?php
                        echo CHtml::textField('email', $email_address
                                , array(
                            'class' => "validate",
                            'data-validation' => "required",
                            'Placeholder' => t("Email")
                        ))
                        ?>
                    </div>
                </div>
            </div><!-- row-->   


            <div class="row">
                <div class="col m6">   
                    <div class="input-form">
                        <?php
                        echo CHtml::passwordField('password', ''
                                , array(
                            'class' => "validate",
                            'data-validation' => "required",
                            'Placeholder' => t("Password")
                        ))
                        ?>
                    </div>
                </div>   
                <div class="col m6">   
                    <div class="input-form">
                        <?php
                        echo CHtml::passwordField('cpassword', ''
                                , array(
                            'class' => "validate",
                            'data-validation' => "required",
                            'Placeholder' => t("Confirmar Password")
                        ))
                        ?>
                    </div>
                </div>   
            </div>  <!--row-->

            <div class="row">


                <div class="col m12">
                    <div class="input-form">
                        <?php
                        echo CHtml::textField('direccion', ''
                                , array(
                            'class' => "validate",
                            'Placeholder' => t("Dirección")
                                //'data-validation'=>"required"
                        ))
                        ?>
                    </div>
                </div>
            </div><!-- row-->      


            <div class="card-action" style="margin-top:20px;">
                <button class="btn waves-effect waves-light" type="submit" name="action">
                    Registro
                </button>
            </div>



        </form>

    </div> <!--container-->
</div> <!--sections-->