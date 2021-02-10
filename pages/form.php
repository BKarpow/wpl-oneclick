<?php if (OneClick::getConfig('ok_debug_trigger')): ?>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<?php else: ?>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@rc/dist/js.cookie.min.js"></script>

<?php define('OK_RECAPTCHA', OneClick::getConfig('ok_recaptcha_trigger')); ?>

<?php if (OK_RECAPTCHA): ?>
<?php define('OK_RECAPTCHA_PUBLIC_KEY',  OneClick::getConfig('ok_recaptcha_public_key')); ?>
<script src="https://www.google.com/recaptcha/api.js?render=<?=OK_RECAPTCHA_PUBLIC_KEY?>"></script>

<?php endif; ?>

<div class="one_click_box" id="app_one_click">
    <label for="phone-field">
    <h4 ref="ok_title" class="animate__animated animate__infinite ">{{test}}</h4>
    </label>
    <div ref="number_box" class="flex-between animate__animated"
         :class="{'<?=OneClick::getConfig('ok_animate_hide_button')?>': hide_box}"
    >

        <div class="col-10">
            <div v-if="lastOrderShow" class="alert alert-success">
                <h2 align="center" style="color:green;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmark-check-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5zm8.854-9.646a.5.5 0 0 0-.708-.708L7.5 7.793 6.354 6.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                    </svg>
                </h2>
                <strong>Ви замовляли цей товар {{ lastOrderDays || 'сьогодні, 0' }} день(днів) тому.</strong>
            </div>
            <div v-if="alert.length" class="alert-box" :class="{'alert-error': error, 'alert-success': !error}">
                {{alert}}
            </div>
            <!-- /.alert-box -->
            <form @submit.prevent="orderClick">
                <input
                        type="tel"
                        id="phone-field"
                        class="form-control"
                        :class="{'error-bg': error}"
                        placeholder="Ваш телефон"
                        v-model="phone"
                >
                <!-- /.form-control -->
            </form>
        </div>
        <!-- /.col-10 -->
        <div class="col-2">
            <button @click="orderClick" class="btn btn-primary btn-lg" id="one_click_go" type="button">Замовити в один клік</button>
        </div>
        <!-- /.col-2 -->
    </div>

</div>
<!-- /.one_click_box -->
<script>
    const url_order = '<?=admin_url("admin-ajax.php?action=ok_send_order")?>';
    var DateDiff = {

        inDays: function(d1, d2) {
            var t2 = d2.getTime();
            var t1 = d1.getTime();

            return parseInt((t2-t1)/(24*3600*1000));
        },

        inWeeks: function(d1, d2) {
            var t2 = d2.getTime();
            var t1 = d1.getTime();

            return parseInt((t2-t1)/(24*3600*1000*7));
        },

        inMonths: function(d1, d2) {
            var d1Y = d1.getFullYear();
            var d2Y = d2.getFullYear();
            var d1M = d1.getMonth();
            var d2M = d2.getMonth();

            return (d2M+12*d2Y)-(d1M+12*d1Y);
        },

        inYears: function(d1, d2) {
            return d2.getFullYear()-d1.getFullYear();
        }
    }
 new Vue({
     el: '#app_one_click',
     data:{
         test: 'Швидке замовлення.',
         phone: '',
         tokenReCaptcha: '',
         lastOrderShow: false,
         lastDateOrder: {},
         lastOrderDays: 0,
         nameCookie: 'alpha_order_id_',
         cookieConfig:{
             expires: 7,
             domain: '.alphashoes.store',
             secure: true,
             sameSite: 'strict'
         },
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
     		const sp = Cookies.get('alpha_my_phone');
     		if (sp){
     			this.phone = sp;
     		}
            const co = Cookies.get(this.nameCookie + this.productData.id)
         if(co){
             this.showLA(co)
         }else{
             this.lastOrderShow = false
         }
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
         showLA(t){
             this.lastDateOrder = new Date(t)
             this.lastOrderDays = DateDiff.inDays(this.lastDateOrder, new Date())
             this.lastOrderShow = true
         },
         orderClick(){
            if (!this.testNumberPhone(this.phone)){
                this.error = true
                this.alert = '<?=OneClick::getConfig('ok_alert_error_number')?>'
                swal(this.alert, '', 'error')
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
                 swal(this.alert, '', 'success')
                 Cookies.set(this.nameCookie + this.productData.id, Date().toString(), this.cookieConfig)
                 Cookies.set('alpha_my_phone', this.productData.phone, { expires: 185, domain: '.alphashoes.store',secure: true,sameSite: 'strict' })
                 this.showLA(Date().toString())
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