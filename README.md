# Mails

> [!IMPORTANT]
> **Vous souhaitez utiliser SymSvel ? Rendez-vous sur [SymSvel CLI](https://github.com/Symsvel-Framework/symsvel-cli).** Ce dépôt est un **composant du framework**, pas un point d'installation autonome. Création de projets, installation des modules et mises à jour passent par la CLI. Les éventuelles commandes Composer, npm ou Symfony décrites ci-dessous concernent le travail des mainteneurs, sauf indication explicite contraire.
>
> [!WARNING]
> **SymSvel est en cours de développement.** Les fonctionnalités et procédures peuvent évoluer ou présenter des anomalies. **Utilisation à vos risques et périls.**

Module SymSvel de configuration des e-mails, sans transport ni logique d'envoi à ce stade.

Dépend de Settings et fournit les définitions `mails.sender_email`, `mails.sender_name` et `mails.transport`. Voir [documentation développeur](docs/dev/index.md).

## Distribution stable v1.0.0 (20/11/2026)

Le manifeste Composer du tag stable déclare exclusivement des contraintes `^1.0` pour ses dépendances SymSvel : `symsvel-framework/module-settings`, `symsvel-framework/module-acls`. Les références `dev-dev` restent réservées à la branche `dev` et à l'installation de maintenance avec `--dev`. Le tag `v1.0.0` n'est à publier **qu'après** le tag stable de chaque dépendance, son indexation dans Satis et la réussite des contrôles CI ; aucune release n'est produite par ce commit.

---

## Participer et soutenir le projet

SymSvel est un projet en cours de développement. Pour contribuer, proposer une amélioration ou échanger avec le mainteneur : **[xavier@xavier-tezza.fr](mailto:xavier@xavier-tezza.fr)**.

SymSvel vous est utile et vous souhaitez me remercier ? **Offrez-moi une bière ! 🍺** (lien PayPal à compléter avant l'ouverture publique).
