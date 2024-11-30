<template>

  <img alt="wallpaper" class="absolute z-10 h-screen object-cover object-[20%_25%] w-auto transform translate-x--1/4" src="/assets/images/wallpaper.png">
  <div class="fixed inset-0 flex items-center justify-center z-50">
    <div class="bg-white bg-opacity-70 p-8 rounded-lg shadow-lg text-center">
      <div class="text-center text-[130px]">🎅</div>

      <div v-if="route.params.token" class="text-lg">
        <div v-if="!ruleAgree">
          <h2 class="text-2xl font-semibold py-2">Willkommen {{ participant.name }}!</h2>
          <p class="pb-3">Wir freuen uns sehr, dass du beim Wichteln mit machst</p>
          <p class="font-bold text-red-800">Bitte beachte die Regeln!</p>
          <p>1. Wichtel nicht verraten!</p>
          <p>2. Link nicht weitergeben!</p>
          <p>3. Das Geschenk sollte max. 15€ kosten!</p>

          <button
              class="bg-green-700 py-2 w-full rounded-xl my-4 text-gray-200 hover:text-white hover:bg-green-600"
              @click="ruleAgree = true"
          >
            Regel akzeptieren
          </button>
        </div>


        <div v-else>
          <h1 class="text-3xl">
            Dein Wichtel:
          </h1>
          <h1 class="text-4xl md:text-6xl font-bold mb-4 text-red-500 drop-shadow-lg">

            {{ participant.chosen_by_decrypt }}
          </h1>
        </div>

        <p class="text-lg md:text-xl text-gray-800 drop-shadow-sm">
          Frohe Weihnachten und viel Freude beim Wichteln! 🎄
        </p>
      </div>


      <div v-else class="text-2xl">
        Es tut mir leid, dein Token funktioniert leider nicht.<br>
        Bitte gib Chris bescheid.
      </div>
    </div>
  </div>


  <div class="h-screen w-full z-50 relative hidden">
    <div>
      Token! {{ route.params.token }} => {{ assignee }}
    </div>


    <div class="max-w-2xl bg-white/.5">
      TEST
    </div>


    <div class="min-h-screen flex flex-col justify-center items-center bg-white text-white">
      <!-- Hintergrund mit Weihnachtsdeko -->
      <div class="absolute inset-0 bg-cover bg-center opacity-40"
           style="background-image: url('https://images.unsplash.com/photo-1544025163-219f3fbf1d99');"></div>
      <!-- Weihnachts-Overlay -->
      <div class="relative z-10 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-4 text-red-500 drop-shadow-lg">
          🎅 Wichtel: {{ name }}
        </h1>
        <p class="text-lg md:text-xl text-gray-300 drop-shadow-sm">
          Frohe Weihnachten und viel Freude beim Wichteln! 🎄
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">

import {useRoute} from "vue-router";
import ParticipantService from "@/services/ParticipantService";
import {onBeforeMount, ref} from "vue";

const route = useRoute()
const service = new ParticipantService()
const participant = ref(null)
const ruleAgree = ref(false)

onBeforeMount(() => {
  if (route.params.token) {
    service.getAssignee(route.params.token).then((response) => {
      participant.value = response.data.data;
    });
  }
});
</script>

<style scoped>

</style>
