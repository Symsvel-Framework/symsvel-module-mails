# Mails

Module SymSvel de configuration des e-mails, sans transport ni logique d'envoi à ce stade.

Dépend de Settings et fournit les définitions `mails.sender_email`, `mails.sender_name` et `mails.transport`. Voir [documentation développeur](docs/dev/index.md).

## Distribution stable v1.0.0 (20/11/2026)

Le manifeste Composer du tag stable déclare exclusivement des contraintes `^1.0` pour ses dépendances SymSvel : `symsvel-framework/module-settings`, `symsvel-framework/module-acls`. Les références `dev-dev` restent réservées à la branche `dev` et à l'installation de maintenance avec `--dev`. Le tag `v1.0.0` n'est à publier **qu'après** le tag stable de chaque dépendance, son indexation dans Satis et la réussite des contrôles CI ; aucune release n'est produite par ce commit.
