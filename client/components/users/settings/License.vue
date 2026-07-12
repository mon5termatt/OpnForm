<template>
  <div class="space-y-6">
    <!-- Bypass fork: don't pretend this is a real SaaS subscription page -->
    <template v-if="isLicenseBypass">
      <div class="license-bypass relative overflow-hidden rounded-xl border border-neutral-800 bg-neutral-950 text-neutral-100 shadow-lg">
        <div class="license-bypass__grid pointer-events-none absolute inset-0 opacity-[0.35]" aria-hidden="true" />
        <div class="license-bypass__glow pointer-events-none absolute -right-16 -top-20 h-56 w-56 rounded-full bg-lime-400/20 blur-3xl" aria-hidden="true" />
        <div class="license-bypass__glow pointer-events-none absolute -bottom-24 -left-10 h-48 w-48 rounded-full bg-cyan-400/15 blur-3xl" aria-hidden="true" />

        <div class="relative space-y-5 p-5 sm:p-6">
          <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-2 max-w-xl">
              <p class="font-mono text-[10px] uppercase tracking-[0.28em] text-lime-300/90">
                Local sovereignty protocol
              </p>
              <h3 class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">
                De-enshittified Edition
              </h3>
              <p class="text-sm leading-relaxed text-neutral-300">
                This instance refuses to phone home for permission to use software you already host.
                Branding gates, seat counters, and “please upgrade” theater: left on the curb in 2026.
              </p>
            </div>
            <div class="shrink-0 rounded-full border border-lime-300/40 bg-lime-300/10 px-3 py-1 font-mono text-[11px] uppercase tracking-wider text-lime-200">
              license://local-bypass
            </div>
          </div>

          <div class="rounded-lg border border-dashed border-lime-300/30 bg-lime-300/5 p-4">
            <p class="font-mono text-[11px] uppercase tracking-wider text-lime-200/90">Reclaimed without invoice</p>
            <ul class="mt-3 grid gap-2 sm:grid-cols-2">
              <li
                v-for="feature in liberatedFeatures"
                :key="feature"
                class="flex items-center gap-2 text-sm text-neutral-200"
              >
                <Icon name="i-heroicons-check-badge" class="h-4 w-4 shrink-0 text-lime-300" />
                <span>{{ feature }}</span>
              </li>
            </ul>
          </div>

          <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs leading-relaxed text-neutral-400 max-w-md">
              Status: <span class="text-lime-300">active forever-ish</span>.
              Billing portal: <span class="line-through decoration-neutral-500">not applicable</span>.
              Dignity: <span class="text-cyan-300">restored locally</span>.
            </p>
            <UButton
              color="neutral"
              variant="outline"
              icon="i-heroicons-face-smile"
              @click="toastLiberation"
            >
              Celebrate anyway
            </UButton>
          </div>
        </div>
      </div>
    </template>

    <template v-else>
      <!-- Header -->
      <div class="flex flex-col flex-wrap items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
          <h3 class="text-lg font-medium text-neutral-900">Enterprise License</h3>
          <p class="mt-1 text-sm text-neutral-500">
            Manage your self-hosted Enterprise license.
          </p>
        </div>

        <div class="flex shrink-0 items-center gap-2">
          <UButton
            label="Help"
            icon="i-heroicons-question-mark-circle"
            variant="outline"
            color="primary"
            @click="crisp.openHelpdeskArticle('self-hosted-license-3ihg7e')"
          />

          <UButton
            v-if="!canAccessEnterprise || isGracePeriod"
            :label="(isExpired || isGracePeriod) ? 'Renew License' : 'Purchase License'"
            icon="i-heroicons-shopping-cart"
            @click="openPurchase"
          />
        </div>
      </div>

      <div v-if="canAccessEnterprise" class="space-y-4">
        <div class="rounded-lg border border-neutral-200 bg-white p-4 sm:p-5 space-y-4">
          <div class="flex items-start justify-between gap-3">
            <div class="flex items-start gap-3">
              <div
                class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full"
                :class="isGracePeriod ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'"
              >
                <Icon
                  :name="isGracePeriod ? 'i-heroicons-clock' : 'i-heroicons-check-circle'"
                  class="h-5 w-5"
                />
              </div>
              <div>
                <p class="text-sm font-semibold text-neutral-900">
                  {{ isGracePeriod ? 'License in grace period' : 'License activated' }}
                </p>
                <p class="mt-1 text-sm text-neutral-600">
                  {{ isGracePeriod ? 'Renew now to avoid losing enterprise features.' : 'Your Enterprise license is active and all licensed features are enabled.' }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="flex flex-wrap gap-3">
          <UButton
            icon="i-heroicons-credit-card"
            :loading="managingSubscription"
            :disabled="managingSubscription"
            @click="openPortal"
          >
            Manage Subscription
          </UButton>
        </div>
      </div>

      <div v-else class="space-y-4">
        <div class="rounded-lg border p-4 sm:p-5 space-y-4" :class="inactiveCardClasses">
          <div class="flex items-start gap-3">
            <div
              class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-full"
              :class="inactiveIconClasses"
            >
              <Icon
                :name="inactiveIcon"
                class="h-5 w-5"
              />
            </div>
            <div>
              <p class="text-sm font-semibold text-neutral-900">
                {{ inactiveTitle }}
              </p>
              <p class="mt-1 text-sm text-neutral-600">
                {{ inactiveDescription }}
              </p>
            </div>
          </div>
        </div>

        <div class="space-y-2">
          <TextInput
            label="License Key"
            name="license_key"
            :form="licenseKeyForm"
            placeholder="lic_xxxxxxxxxxxxxxxxxxxxxxxx"
          />
          <UButton
            :loading="activating"
            :disabled="!licenseKeyForm.license_key || activating"
            icon="i-heroicons-check-circle"
            @click="activateLicense"
          >
            Activate License
          </UButton>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { licenseApi } from '~/api'
import { useInstanceLicense } from '~/composables/useInstanceLicense'

const crisp = useCrisp()
const alert = useAlert()
const { invalidateFlags } = useFeatureFlags()
const { invalidateUser } = useAuth()
const { openSubscriptionModal } = useAppModals()

const {
  hasLicense,
  canAccessEnterprise,
  isGracePeriod,
  isExpired,
  isActivationLimitReached,
  isLicenseBypass,
} = useInstanceLicense()

const activating = ref(false)
const managingSubscription = ref(false)
const licenseKeyForm = useForm({
  license_key: '',
})

const liberatedFeatures = [
  'No OpnForm branding tax',
  'Custom domains without begging',
  'Slack / Discord / SMTP unlocked',
  'Seat limits politely ignored',
  'Custom code & white-label bits',
  'Zero phone-home license drama',
]

const celebrationLines = [
  'Invoice amount: $0.00 — paid in spite.',
  'Congratulations. Capitalism failed to reach this container.',
  'Enterprise features acquired the old-fashioned way: by reading the source.',
  'Your wallet remains un-enshittified.',
]

const toastLiberation = () => {
  const line = celebrationLines[Math.floor(Math.random() * celebrationLines.length)]
  alert.success(line)
}

const openPurchase = () => {
  openSubscriptionModal({ plan: 'self_hosted' })
}

const inactiveCardClasses = computed(() => {
  return hasLicense.value ? 'border-red-200 bg-red-50/40' : 'border-neutral-200 bg-white'
})

const inactiveIconClasses = computed(() => {
  return hasLicense.value ? 'bg-red-100 text-red-600' : 'bg-neutral-100 text-neutral-500'
})

const inactiveIcon = computed(() => {
  return hasLicense.value ? 'i-heroicons-exclamation-triangle' : 'i-heroicons-key'
})

const inactiveTitle = computed(() => {
  if (isActivationLimitReached.value) return 'License Already Activated'
  if (isExpired.value) return 'License Expired'
  if (hasLicense.value) return 'License Inactive'
  return 'No Active License'
})

const inactiveDescription = computed(() => {
  if (isActivationLimitReached.value) {
    return 'This license key is already activated on another self-hosted instance. Contact support to reset it or activate a different key.'
  }
  if (isExpired.value) {
    return 'Enterprise features are currently disabled. Renew or activate a valid license key to restore access.'
  }
  if (hasLicense.value) {
    return 'Enterprise features are currently disabled. Activate a valid license key to restore access.'
  }
  return 'Enter your license key below to enable Enterprise features for this self-hosted instance.'
})

const openPortal = () => {
  managingSubscription.value = true
  licenseApi.portal().then((response) => {
    window.open(response.portalUrl, '_blank')
  }).catch((error) => {
    alert.error(error?.data?.message || 'Failed to open billing portal.')
  }).finally(() => {
    managingSubscription.value = false
  })
}

const activateLicense = () => {
  activating.value = true
  licenseApi.activate(licenseKeyForm.license_key).then((result) => {
    if (result.status === 'active') {
      alert.success(result.message)
      return invalidateFlags().then(() => invalidateUser())
    } else {
      alert.error(result.message)
    }
  }).catch((error) => {
    alert.error(error?.data?.message || 'Failed to activate license. Please check your key.')
  }).finally(() => {
    activating.value = false
  })
}
</script>

<style scoped>
.license-bypass__grid {
  background-image:
    linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
  background-size: 24px 24px;
  mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%);
}
</style>
