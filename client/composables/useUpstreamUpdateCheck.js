import { createSharedComposable } from '@vueuse/core'

/**
 * Compare this self-hosted build against upstream OpnForm GitHub releases.
 * Used by the license-bypass settings UI so forks can see when to sync manually.
 */

const CACHE_TTL_MS = 6 * 60 * 60 * 1000 // 6 hours
const CACHE_KEY = 'opnform_upstream_release_check'

function normalizeVersion(raw) {
  if (!raw || typeof raw !== 'string') return null
  const match = raw.trim().match(/v?(\d+(?:\.\d+){0,3})/i)
  return match ? match[1] : null
}

function parseVersionParts(version) {
  return String(version)
    .split('.')
    .map((part) => {
      const n = parseInt(part, 10)
      return Number.isFinite(n) ? n : 0
    })
}

/**
 * @returns {number} positive if a > b, negative if a < b, 0 if equal
 */
function compareSemver(a, b) {
  const left = parseVersionParts(a)
  const right = parseVersionParts(b)
  const len = Math.max(left.length, right.length)
  for (let i = 0; i < len; i += 1) {
    const diff = (left[i] || 0) - (right[i] || 0)
    if (diff !== 0) return diff
  }
  return 0
}

function repoSlugFromUrl(url) {
  if (!url) return null
  try {
    const parsed = new URL(url)
    const parts = parsed.pathname.replace(/^\/+|\/+$/g, '').split('/')
    if (parts.length < 2) return null
    return `${parts[0]}/${parts[1].replace(/\.git$/, '')}`
  } catch {
    return null
  }
}

function readCache() {
  if (!import.meta.client) return null
  try {
    const raw = localStorage.getItem(CACHE_KEY)
    if (!raw) return null
    const parsed = JSON.parse(raw)
    if (!parsed?.checkedAt || Date.now() - parsed.checkedAt > CACHE_TTL_MS) {
      return null
    }
    return parsed
  } catch {
    return null
  }
}

function writeCache(payload) {
  if (!import.meta.client) return
  try {
    localStorage.setItem(CACHE_KEY, JSON.stringify(payload))
  } catch {
    // ignore quota / private mode
  }
}

export const useUpstreamUpdateCheck = createSharedComposable(() => {
  const runtimeConfig = useRuntimeConfig()
  const currentVersion = computed(() => normalizeVersion(useFeatureFlag('version')))

  const upstreamRepoUrl = computed(() => {
    return String(runtimeConfig.public.upstreamGithubRepoUrl || 'https://github.com/OpnForm/OpnForm')
      .replace(/\/$/, '')
  })

  const upstreamSlug = computed(() => repoSlugFromUrl(upstreamRepoUrl.value) || 'OpnForm/OpnForm')

  const isLoading = ref(false)
  const error = ref(null)
  const latestVersion = ref(null)
  const latestTag = ref(null)
  const releaseUrl = ref(null)
  const releaseName = ref(null)
  const checkedAt = ref(null)

  const hasUpdate = computed(() => {
    if (!currentVersion.value || !latestVersion.value) return false
    return compareSemver(latestVersion.value, currentVersion.value) > 0
  })

  const isUpToDate = computed(() => {
    if (!currentVersion.value || !latestVersion.value) return false
    return compareSemver(latestVersion.value, currentVersion.value) <= 0
  })

  const compareUrl = computed(() => {
    if (!currentVersion.value || !latestTag.value) return `${upstreamRepoUrl.value}/releases`
    return `${upstreamRepoUrl.value}/compare/v${currentVersion.value}...${latestTag.value}`
  })

  const releasesUrl = computed(() => `${upstreamRepoUrl.value}/releases`)

  function applyResult(result) {
    latestVersion.value = result.latestVersion
    latestTag.value = result.latestTag
    releaseUrl.value = result.releaseUrl
    releaseName.value = result.releaseName
    checkedAt.value = result.checkedAt
    error.value = result.error || null
  }

  function check(options = {}) {
    const force = options.force === true
    if (!import.meta.client) return Promise.resolve()

    if (!force) {
      const cached = readCache()
      if (cached) {
        applyResult(cached)
        return Promise.resolve(cached)
      }
    }

    isLoading.value = true
    error.value = null

    return $fetch(`https://api.github.com/repos/${upstreamSlug.value}/releases/latest`, {
      headers: { Accept: 'application/vnd.github+json' },
    }).then((release) => {
      const tag = release?.tag_name || null
      const version = normalizeVersion(tag)
      const result = {
        latestVersion: version,
        latestTag: tag,
        releaseUrl: release?.html_url || releasesUrl.value,
        releaseName: release?.name || tag,
        checkedAt: Date.now(),
        error: version ? null : 'Could not parse upstream version',
      }
      writeCache(result)
      applyResult(result)
      return result
    }).catch((err) => {
      const result = {
        latestVersion: null,
        latestTag: null,
        releaseUrl: releasesUrl.value,
        releaseName: null,
        checkedAt: Date.now(),
        error: err?.data?.message || err?.message || 'Failed to check upstream releases',
      }
      applyResult(result)
      return result
    }).finally(() => {
      isLoading.value = false
    })
  }

  return {
    currentVersion,
    latestVersion,
    latestTag,
    releaseUrl,
    releaseName,
    releasesUrl,
    compareUrl,
    upstreamRepoUrl,
    hasUpdate,
    isUpToDate,
    isLoading,
    error,
    checkedAt,
    check,
  }
})
