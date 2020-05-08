<template>
  <div class="q-pa-md">
    <div class="q-gutter-sm">
      <q-dialog v-model="showImageByDoctor">
        <q-card style="width: 800px; max-width: 90vw;">
          <q-bar class="bg-blue text-white">
            <q-icon name="mail"></q-icon>
            <div>{{toolbarLabel}}</div>

            <q-space></q-space>

            <q-btn dense flat icon="close" v-close-popup>
              <q-tooltip>Cerrar</q-tooltip>
            </q-btn>
          </q-bar>

          <q-card-section>
            <q-img :src="imageSource" spinner-color="white">
              <template v-slot:error>
                <div
                  class="absolute-full flex flex-center bg-negative text-white"
                >No se pudo cargar la imagen</div>
              </template>
            </q-img>
          </q-card-section>
        </q-card>
      </q-dialog>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isReadOnly: true,
      showImageByDoctor: false,
      toolbarLabel: "IMAGEN",
      comments: null,
      imageSource: null
    };
  },
  components: {},
  methods: {
    open(imageAttributes) {
      let vm = this;
      vm.imageSource = imageAttributes.file_name;
      vm.comments = imageAttributes.other_details;
      vm.showImageByDoctor = true;
    }
  }
};
</script>
