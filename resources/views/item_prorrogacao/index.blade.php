<x-layout.principal>
    <div class="mt-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary mb-0">
                 Item Prorrogacaos
            </h2>
            <div class="d-flex">
                <div class="ms-2">
                    <popup-button id="nova-item-prorrogacao" title="Nova Item Prorrogacao"
                                  component="item-prorrogacao-form" action="/item_prorrogacao/" size="xl">
                        <i class="fa fa-plus"></i>
                        Nova Item Prorrogacao
                    </popup-button>
                </div>
            </div>

        </div>

        <div class="card">
            <div class="card-body">
                <item-prorrogacao-grid>
                </item-prorrogacao-grid>
            </div>
        </div>
    </div>
</x-layout.principal>
