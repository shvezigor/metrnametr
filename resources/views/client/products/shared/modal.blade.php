<!-- BEGIN Order form -->
<div class="modal fade order-form" id="order-form" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="wrap-modal-dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="inner-wrap">
                    <div class="modal-header">
                        <button type="button"  class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title">Форма замовлення</h3>
                    </div>
                    <div class="modal-body">
                        <p>Залиште свої контактні дані і ми зв’яжемось з вами протягом години</p>
                        <form action="{{ route('orders') }}" method="POST">

                            @csrf

                            <input name="product" type="hidden">

                            <input name="name" type="text" placeholder="Ваше ім’я">
                            <input name="phone" type="text" class="phone" placeholder="Телефон" data-validation="number" data-validation-allowing="float">

                            <button type="submit" class="yellow-btn blue-hover">Надіслати</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Order form -->
