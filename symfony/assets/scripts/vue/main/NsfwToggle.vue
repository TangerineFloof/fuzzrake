<template>
  <button
    type="button"
    class="btn btn-success text-nowrap"
    data-bs-toggle="modal"
    data-bs-target="#filtersModal"
  >
    NSFW
  </button>
</template>

<script lang="ts">
import AgeAndSfwConfig from "../../class/AgeAndSfwConfig";
import { Options, Vue } from "vue-class-component";

class NsfwToggle extends Vue {
  private readonly aasConfig: AgeAndSfwConfig = AgeAndSfwConfig.getInstance();
}

export default NsfwToggle;
</script>
