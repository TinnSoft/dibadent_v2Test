<template>
  <q-page padding>
    <div >
      <!--<q-card inline>
        <q-card-section>
          <q-icon slot="right" size="42px" name="sentiment_dissatisfied" />
        </q-card-section>
        <q-card>
          <div
            class="q-mb-md"
            :class="[`q-display-1`, `q-display-1-opacity`]"
          >{{ $t('page_not_found') }}</div>
        </q-card>
        <q-card-separator />
        <q-card-actions>
          <q-btn flat color="secondary" :to="{ name: 'home' }" label="Ir al inicio" />
        </q-card-actions>
      </q-card>-->
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
  </q-page>
</template>

<script>
export default {
  name: "NotFound"
};
</script>