<template>
  <div class="space-y-4">
    <div>
      <h3 class="text-lg font-medium text-neutral-900">Upstream updates</h3>
      <p class="mt-1 text-sm text-neutral-500">
        Version status against upstream OpnForm.
      </p>
    </div>

    <div
      class="rounded-lg border p-4 sm:p-5 space-y-4"
      :class="hasUpdate ? 'border-amber-200 bg-amber-50/50' : 'border-neutral-200 bg-white'"
    >
      <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
        <div>
          <p class="text-xs text-neutral-500">Running</p>
          <p class="font-medium text-neutral-900">
            {{ currentVersion ? `v${currentVersion}` : 'unknown' }}
          </p>
        </div>
        <div>
          <p class="text-xs text-neutral-500">Latest upstream</p>
          <p class="font-medium text-neutral-900">
            <template v-if="isLoading">Checking…</template>
            <template v-else-if="latestVersion">v{{ latestVersion }}</template>
            <template v-else>—</template>
          </p>
        </div>
        <div>
          <p class="text-xs text-neutral-500">Status</p>
          <p
            class="font-medium"
            :class="{
              'text-amber-800': hasUpdate,
              'text-emerald-700': isUpToDate,
              'text-red-600': !!updateError && !isLoading,
              'text-neutral-500': isLoading && !hasUpdate && !isUpToDate,
            }"
          >
            <template v-if="isLoading">Checking…</template>
            <template v-else-if="hasUpdate">Update available</template>
            <template v-else-if="isUpToDate">Up to date</template>
            <template v-else-if="updateError">{{ updateError }}</template>
            <template v-else>—</template>
          </p>
        </div>
      </div>

      <div class="flex flex-wrap gap-2">
        <UButton
          color="neutral"
          variant="outline"
          size="sm"
          icon="i-heroicons-tag"
          :href="releasesUrl"
          target="_blank"
        >
          Upstream releases
        </UButton>
        <UButton
          color="neutral"
          variant="ghost"
          size="sm"
          icon="i-heroicons-arrow-path"
          :loading="isLoading"
          @click="refreshUpstreamCheck"
        >
          Check again
        </UButton>
      </div>
    </div>
  </div>
</template>

<script setup>
const {
  currentVersion,
  latestVersion,
  hasUpdate,
  isUpToDate,
  isLoading,
  error: updateError,
  releasesUrl,
  check: checkUpstream,
} = useUpstreamUpdateCheck()

onMounted(() => {
  checkUpstream()
})

const refreshUpstreamCheck = () => {
  checkUpstream({ force: true })
}
</script>
