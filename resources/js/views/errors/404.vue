<template>
  <!--<q-page padding>
    <div >
      <q-banner class="bg-grey-3">
        <template v-slot:avatar>
          <q-icon name="sentiment_very_dissatisfied" color="primary" />
        </template>
        {{ $t('page_not_found') }}
        <template v-slot:action>
          <q-btn flat color="primary" :to="{ name: 'home' }" label="Regresar" />
        </template>
      </q-banner>
    </div>
  </q-page>-->
  <div class="fixed-center text-center">
    <p>
      <q-icon name="sentiment_very_dissatisfied"  style="color: #ccc;font-size: 8em;" />
    </p>
    <p class="text-faded">{{ $t('page_not_found') }}<strong>(404)</strong></p>
    <q-btn flat color="primary" :to="{ name: 'home' }" label="Regresar" style="width:200px;" />
  
  </div>
</template>

<script>
export default {
  name: "NotFound"
};
</script>