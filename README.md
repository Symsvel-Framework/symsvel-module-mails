# SymSvel Mails

> [!IMPORTANT]
> **Vous souhaitez utiliser SymSvel ? Rendez-vous sur [SymSvel CLI](https://github.com/Symsvel-Framework/symsvel-cli).** Ce dépôt est un **composant du framework**, pas un point d'installation autonome. Création de projets, installation des modules et mises à jour passent par la CLI. Les éventuelles commandes Composer, npm ou Symfony décrites ci-dessous concernent le travail des mainteneurs, sauf indication explicite contraire.

> [!WARNING]
> **SymSvel est en cours de développement.** Les fonctionnalités et procédures peuvent évoluer ou présenter des anomalies. **Utilisation à vos risques et périls.**

Module de **configuration et d'envoi des e-mails transactionnels** : identité expéditrice via Settings, registre de templates et rendu, intégration à Symfony Mailer. La personnalisation des templates depuis le BO fait l'objet d'un chantier distinct (`Mail Custom`).

Dépend de Settings et déclare notamment `mails.sender_email`, `mails.sender_name` et `mails.transport`. Voir la [documentation développeur](docs/dev/index.md).

---

## Intégration continue GitHub-hosted (22/11/2026)

Le workflow public `.github/workflows/qa.yml` utilise désormais **GitHub-hosted Ubuntu 24.04** au lieu des runners Docker du mainteneur. Les tests Composer et PHP 8.4 restent actifs. Les tests d'interface installent Chromium et ses dépendances système lorsque nécessaire.

Les runners auto-hébergés sont réservés à `symsvel-packages` et `symsvel-recipes`, qui restent privés. Une CI verte ne dispense pas des crash-tests locaux.

## Licence MIT et contributions

Ce dépôt est distribué sous [licence MIT](LICENSE). Le code peut être utilisé, modifié et redistribué, y compris dans des projets commerciaux ou propriétaires, dans les conditions de cette licence (notamment la conservation de la notice de copyright et de licence).

**Contribuer :** créer un fork, puis proposer une pull request vers `dev` lorsque cette branche existe, sinon vers la branche par défaut. Expliquer la modification et fournir les tests et la documentation concernés. Les mainteneurs conservent la maîtrise des revues, des validations (CI et crash-tests), de l'intégration et des releases officielles. Une PR ne garantit pas son acceptation.

**Versions officielles :** seules les versions diffusées par l'organisation [Symsvel-Framework](https://github.com/Symsvel-Framework) sont officielles. Les forks sont libres sous MIT, mais ne doivent pas être présentés comme officiellement approuvés par les mainteneurs. Cette distinction n'ajoute aucune restriction aux droits accordés par la licence MIT sur le code.

## Participer et soutenir le projet

SymSvel est un projet en cours de développement. Pour contribuer, proposer une amélioration ou échanger avec le mainteneur : **[xavier@xavier-tezza.fr](mailto:xavier@xavier-tezza.fr)**.

SymSvel vous est utile et vous souhaitez me remercier ? **Offrez-moi une bière ! 🍺** (lien PayPal à compléter avant l'ouverture publique).

## Promotion stable (24/11/2026)

Les contraintes Composer de publication sont stables (`^1.0`), sans modifier le canal `dev`. Publier après les dépendances, leur indexation et les CI vertes. CT stable distinct.
