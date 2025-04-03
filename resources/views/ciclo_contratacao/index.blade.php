<x-layout.principal>
    <div class="mt-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary mb-0">
                 Ciclo Contratacaos
            </h2>
            <div class="d-flex">
                <div class="ms-2">
                    <popup-button id="nova-ciclo-contratacao" title="Nova Ciclo Contratacao"
                                  component="ciclo-contratacao-form" action="/ciclo_contratacao/" size="xl">
                        <i class="fa fa-plus"></i>
                        Nova Ciclo Contratacao
                    </popup-button>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-body">
                <ciclo-contratacao-grid>
                </ciclo-contratacao-grid>
            </div>
        </div>
    </div>
</x-layout.principal>
