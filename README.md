# OpnForm (de-enshittified fork)

<p align="center">
<img src="https://github.com/OpnForm/OpnForm/blob/main/client/public/img/social-preview.jpg?raw=true">
</p>

> **Unofficial fork.** This branch is maintained for people who self-host OpnForm and want the product to stay useful on their own hardware, without a license server deciding which basics they are allowed to run.

Upstream OpnForm remains an excellent open-source form builder. This fork exists because self-hosted software keeps drifting toward the same tired pattern: charge for essentials, gate integrations, phone home for permission, and ship paid tiers that quietly break.

## An open letter to OpnForm

Please do the right thing.

Self-hosting customers are not freeloaders. They run your code, report bugs, fix edge cases, and keep the AGPL ecosystem alive. When core self-hosted capabilities (removing branding, connecting Slack or Discord, using a custom domain, adding a few seats, enabling IP tracking) become paywalled “Enterprise” checkboxes, the product starts to feel less open and more extracted.

Worse: when a paid feature ships broken, customers do not get a careful error. They get a dead button and a generic failure. That is how trust erodes.

A healthier path still exists:

1. **Keep the AGPL core genuinely capable.** Fund hosting, support, and managed cloud with service quality, not by locking ordinary self-host features behind a meter.
2. **If a feature is sold, make sure it works.** Paid tiers should be the best-tested paths in the product, not the least.
3. **Prefer honesty over dark patterns.** Clear licensing beats silent phone-home gates and surprise entitlement cliffs.
4. **Treat self-hosters as partners.** People choose FOSS because free means freedom: to run the software, study it, fix it, and keep control of their own stack. They will not forever accept being treated like a leakage problem.

Enshittification is optional. You can still choose not to.

This fork is not a vendetta. It is a pressure valve: local license checks are bypassed so operators can run a complete self-hosted instance, keep shipping fixes, and stay synced with upstream releases. Prefer the [official project](https://github.com/OpnForm/OpnForm) and [managed cloud](https://opnform.com/) when that model fits. Prefer this fork when you need software you host to remain usable.

OpnForm’s Enterprise terms still apply to proprietary Enterprise code if you use those features in production. For official licensing, see [OpnForm’s self-hosted license docs](https://docs.opnform.com/deployment/self-hosted-license).

## What this fork changes

- Bypasses self-hosted Enterprise license-server validation locally so feature gates do not depend on phone-home activation.
- Keeps upstream syncs while preserving the license-bypass path.
- Ships practical fixes found while operating self-hosted instances (for example, submission CSV export failing on forms with IP tracking enabled because the UI sent an `ip_address` column the export API rejected).
- Publishes container images for this branch via GHCR for operators who want the patched build.

This is not affiliated with or endorsed by the OpnForm authors.

## Upstream OpnForm

OpnForm is an open-source form builder.

### Get Started

The easiest official path is the [managed cloud service](https://opnform.com/). For self-hosted installs, see the [Deployment Guides](https://docs.opnform.com/deployment) and [Docker Development Guide](https://docs.opnform.com/deployment/docker-development).

### Key Features

- 🚀 No-code builder with unlimited forms & submissions
- 📝 Various input types: Text, Date, URL, File uploads & much more
- 🌐 Embed anywhere
- 📧 Email notifications
- 💬 Integrations (Slack, Webhooks, Discord)
- 🧠 Form logic & customization
- 🛡️ Captcha protection
- 📊 Form analytics

Full documentation: [docs.opnform.com](https://docs.opnform.com).

### Codex worktrees

Codex worktrees use an isolated PostgreSQL volume and local Laravel/Nuxt processes. Use `./scripts/codex-worktree-setup.sh` and `./scripts/codex-worktree-up.sh`, or the Codex Start/Reset/Stop actions. Seeded admin: `e2e@example.test` / `Abcd@1234`.

### Support & Community (upstream)

- [Discord](https://discord.gg/YTSjU2a9TS)
- [Product Helpdesk](https://help.opnform.com)
- [Technical Documentation](https://docs.opnform.com)

### License

OpnForm is open-source under the GNU Affero General Public License Version 3 (AGPLv3) or any later version. See [LICENSE](https://github.com/OpnForm/OpnForm/blob/main/LICENSE).

Upstream also uses a dual-license model: core under AGPL-3.0, with advanced features under `api/app/Enterprise/` covered by OpnForm’s Enterprise terms. This fork does not change that legal reality; it only changes how self-hosted entitlement checks behave in practice on this branch.
