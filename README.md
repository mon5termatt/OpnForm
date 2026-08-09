# OpnForm (de-enshittified FOSS fork)

<p align="center">
<img src="https://github.com/OpnForm/OpnForm/blob/main/client/public/img/social-preview.jpg?raw=true">
</p>

> **Unofficial FOSS fork.** Free software means freedom: to run it, study it, share it, and improve it on hardware you control. This branch exists so self-hosted OpnForm stays usable as free software, without a license server deciding which basics you are allowed to run.

Upstream OpnForm is still a strong form builder. This fork exists because too much of the self-hosted path is drifting away from FOSS norms: essentials locked behind paywalls, integrations metered, phone-home entitlement checks, and “Enterprise” features that do not reliably work.

FOSS users are not a conversion funnel. They chose free software on purpose.

## An open letter to OpnForm

Please do the right thing. Stop enshittifying a FOSS project.

People who want FOSS want **free**. Free as in freedom, and free as in not renting back the right to run software they already host. Self-hosters keep the AGPL ecosystem alive. They deploy your code, find the sharp edges, and fix things in production. Treating them like leakage is how open projects lose their communities.

When ordinary self-hosted capabilities (removing branding, connecting Slack or Discord, using a custom domain, adding a few seats, enabling IP tracking) become paywalled checkboxes, the software stops feeling free. When those paid features ship broken, it stops feeling honest.

### On bugs, issues, and PRs

While running this software in production, I have found **multiple** real bugs. I am fixing them here for operators who need a working self-hosted instance.

I will **not** open upstream issues or pull requests for those fixes until this de-enshittification stops.

That is intentional. Free software communities improve projects by reporting and contributing. Paywalled entitlement theater breaks that social contract. If upstream wants the bugfixes, restore a FOSS-respecting self-hosted model first: a capable AGPL core, no phone-home gatekeeping for basics, and paid offerings that are optional services rather than locks on freedom.

A healthier path still exists:

1. **Keep the AGPL core genuinely free and capable.** Cloud hosting and support can be businesses. Locking ordinary self-host features behind a meter is not FOSS stewardship.
2. **If a feature is sold, make sure it works.** Paid tiers should be the best-tested paths in the product, not the least.
3. **Prefer honesty over dark patterns.** Clear licensing beats silent phone-home gates and surprise entitlement cliffs.
4. **Treat FOSS users as the point, not the problem.** People choose this stack because free means freedom: to run the software, study it, fix it, and keep control of their own data and infrastructure.

Enshittification is optional. You can still choose not to.

This fork is not a vendetta. It is a FOSS pressure valve: local license checks are bypassed so operators can run a complete self-hosted instance, keep shipping fixes, and stay synced with upstream releases. Prefer the [official project](https://github.com/OpnForm/OpnForm) and [managed cloud](https://opnform.com/) when that model fits. Prefer this fork when you need free software you host to remain free in practice.

OpnForm’s Enterprise terms still apply to proprietary Enterprise code if you use those features in production. For official licensing, see [OpnForm’s self-hosted license docs](https://docs.opnform.com/deployment/self-hosted-license).

## What this fork changes

- Restores a FOSS-first self-hosted posture by bypassing local Enterprise license-server validation, so feature gates do not depend on phone-home activation.
- Defaults anonymous telemetry **off** (no usage events to `telemetry.opnform.com` unless you explicitly opt in).
- Keeps upstream syncs while preserving the license-bypass path.
- Ships practical production fixes found while operating self-hosted instances. One example: submission CSV export failed on forms with IP tracking enabled because the UI sent an `ip_address` column the export API rejected. There are others. They stay here until upstream changes course.
- Publishes container images for this branch via GHCR for operators who want the patched FOSS build.

This is not affiliated with or endorsed by the OpnForm authors.

## Upstream OpnForm

OpnForm is an AGPL form builder: [repo](https://github.com/OpnForm/OpnForm), [cloud](https://opnform.com/), [docs](https://docs.opnform.com), [Discord](https://discord.gg/YTSjU2a9TS).

Core is free software under AGPLv3 (or later); see [LICENSE](https://github.com/OpnForm/OpnForm/blob/main/LICENSE). Upstream also dual-licenses advanced code under `api/app/Enterprise/` with proprietary Enterprise terms. This fork does not rewrite that legal reality. It exists so FOSS self-hosters can keep running a capable instance without pretending phone-home paywalls are compatible with free software norms.
