<div class="col-12 col-md-12  col-xl-12 d-flex">
    <div class="card wallet-widget">
        <div class="circle-bar circle-bar3">
            <div class="circle-graph3" data-percent="{{ $timeRemainingPercentage }}">
                <b><img src="{{ URL::asset('/assets/img/icons/timer.svg') }}" alt=""></b>
            </div>
        </div>
        <div class="main-limit">
            <p>{{__('Próxima cita')}}</p>
            <h4>{{ $nextAppointmentTime }}</h4>
        </div>
    </div>
    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setInterval(function() {
                Livewire.dispatch('getNextAppointment');
                console.log('actualizó next appointment'); // Actualizar cada minuto
                animateElements();
                $(window).scroll(animateElements);
            }, 30000);

            animateElements();
        });


                    function animateElements() {
                $('.circle-bar3').each(function () {
                    var elementPos = $(this).offset().top;
                    var topOfWindow = $(window).scrollTop();
                    var percent = $(this).find('.circle-graph3').attr('data-percent');
                    var animate = $(this).data('animate');
                    if (elementPos < topOfWindow + $(window).height() - 30 && !animate && percent) {
                        $(this).data('animate', true);
                        $(this).find('.circle-graph3').circleProgress({
                            value: percent / 100,
                            size : 400,
                            thickness: 30,
                            fill: {
                                color: '#2E37A4'
                            }
                        });
                    }
                });
            }

    //$(window).scroll(animateElements);
        animateElements();
    </script>
@endpush
</div>
