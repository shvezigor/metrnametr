@if(!empty($faq))
    <section class="seo-faq">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="small-title black">Популярні питання</div>
                </div>

                <div class="col-xs-12 col-md-10 col-md-offset-1">
                    @foreach($faq as $item)
                        <div class="seo-faq-item">
                            <h3>{{ $item['question'] }}</h3>
                            <p>{{ $item['answer'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
