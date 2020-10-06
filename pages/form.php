<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<?php define('OK_RECAPTCHA', OneClick::getConfig('ok_recaptcha_trigger')); ?>

<?php if (OK_RECAPTCHA): ?>
<?php define('OK_RECAPTCHA_PUBLIC_KEY',  OneClick::getConfig('ok_recaptcha_public_key')); ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?=OK_RECAPTCHA_PUBLIC_KEY?>"></script>

<?php endif; ?>

<div class="one_click_box" id="app_one_click">

    <h4 ref="ok_title" class="animate__animated animate__infinite ">{{test}}</h4>
    <div ref="number_box" class="flex-between animate__animated"
         :class="{'<?=OneClick::getConfig('ok_animate_hide_button')?>': hide_box}"
    >
        <div class="col-10">
            <input
                    type="tel"
                    class="form-control"
                    :class="{'error-bg': error}"
                    placeholder="Ваш телефон"
                    v-model="phone"
            >
            <!-- /.form-control -->
        </div>
        <!-- /.col-10 -->
        <div class="col-2">
            <button @click="orderClick" type="button">Замовити</button>
        </div>
        <!-- /.col-2 -->
    </div>
    <div v-if="alert.length" class="alert-box" :class="{'alert-error': error, 'alert-success': !error}">
        {{alert}}
    </div>
    <!-- /.alert-box -->
</div>
<!-- /.one_click_box -->
<script>
    const url_order = '<?=admin_url("admin-ajax.php?action=ok_send_order")?>';
 new Vue({
     el: '#app_one_click',
     data:{
         test: 'Замовити в один клік',
         phone: '',
         tokenReCaptcha: '',
         productData: {
             id: '<?=self::$product_info['id']?>',
             price: '<?=self::$product_info['price']?>',
             name: '<?=self::$product_info['name']?>',
             url: '<?=self::$product_info['url']?>',
             phone: ''
         },
         alert: '',
         error: false,
         hide_box: false,
     },
     watch:{
         phone(){
             if (this.phone.length >= 9){
                 if (!this.testNumberPhone(this.phone)){
                     this.error = true
                     this.alert = '<?=OneClick::getConfig('ok_alert_error_number')?>'
                 }else{
                     this.error = false
                     this.alert = ''
                 }
             }else{
                 this.error = false
                 this.alert = ''
             }
         }
     },
     mounted(){

         <? if ($ok_animate_title = OneClick::getConfig('ok_animate_title')): ?>
         this.$refs.ok_title.classList.add('<?=$ok_animate_title?>')
         <?endif;?>

         <?php if (OK_RECAPTCHA):?>
         grecaptcha.ready(function() {
             grecaptcha.execute('<?=OK_RECAPTCHA_PUBLIC_KEY?>', {action: 'submit'}).then(function (token) {
                 this.tokenReCaptcha = token
                 <? if(OneClick::getConfig('ok_debug_trigger')): ?>
                 console.log("Token recaptcha", token)
                 <? endif; ?>
             }.bind(this));
         }.bind(this));
         <? endif; ?>
     },
     methods:{
         orderClick(){
            if (!this.testNumberPhone(this.phone)){
                this.error = true
                this.alert = '<?=OneClick::getConfig('ok_alert_error_number')?>'
            }else{
                this.orderFromPhone()
            }
         },
         testNumberPhone(phone){
             const reg = /^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){10,14}(\s*)?$/i
             return reg.test(phone)
         },
         recaptcha(){

         },

         orderFromPhone(){
             this.recaptcha()
             this.productData.phone = this.phone
             this.productData.token = this.tokenReCaptcha
             axios.post(url_order, this.productData).then(r => {
                 <?php if (!OneClick::getConfig('ok_debug_trigger')): ?>
                 this.hide_box = true
                 this.$refs.ok_title.classList.remove('animate__animated')
                 setTimeout(()=>{
                     this.$refs.number_box.style.height = '0px'
                 }, 800)
                 this.alert = '<?=OneClick::getConfig('ok_alert_success')?>'
                 <?php else: ?>
                 this.alert = 'Debug: ' + JSON.stringify(r.data)
                 <?php endif;  ?>

             }).catch(err => {
                 this.error = true
                 this.alert = err.toString()
                 console.error(err)
             })
         }
     }
 });
</script>