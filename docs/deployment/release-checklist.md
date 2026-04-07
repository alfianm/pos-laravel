# Release Checklist

## 1. Pre-Release (Development)
- [X] All features implemented according to PRD.
- [X] All tests passed (160+ tests).
- [X] No critical Lint errors.
- [X] UI tested for responsiveness (Desktop, Tablet, Mobile).
- [X] Dark mode and light mode visual verification.

## 2. Release Candidate (Staging)
- [ ] Database migrations tested on real data snapshot.
- [ ] Asset buildup and delivery verified.
- [ ] Marketplace API tokens integration tests with sandbox.
- [ ] Performance check for high volume sales (1,000+ entries).

## 3. Production Release (Launch)
- [ ] Backup current production database.
- [ ] Pull latest code changes using Git.
- [ ] Run `composer install --no-dev`.
- [ ] Run `npm install && npm run build`.
- [ ] Run `php artisan migrate --force`.
- [ ] Clear and rebuild application cache.
- [ ] Restart Supervisor workers.
- [ ] Health check: verify `/up` endpoint and dashboard accessibility.
