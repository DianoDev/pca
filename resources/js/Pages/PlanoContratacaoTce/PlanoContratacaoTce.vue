<template>
    <LayoutPrincipal>
        <div class="">

oi
        </div>
    </LayoutPrincipal>
</template>

<script setup>
import {ref, inject, computed, onMounted, watch} from 'vue';
import PopupButton from "@/Components/PopupButton.vue";
import LayoutPrincipal from "@/Layouts/LayoutPrincipal.vue";
import ItemContratacaoGrid from "@/Pages/ItemContratacao/ItemContratacaoGrid.vue";
import ItemProrrogacaoGrid from "@/Pages/ItemProrrogacao/ItemProrrogacaoGrid.vue";

const events = inject('events');

// Event listeners
onMounted(async () => {

});
</script>
