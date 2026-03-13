📄 Le dashboard Power BI complet (Profils, Métabolique, Géographie) est disponible en PDF via le lien ci-dessous. Une fois sur GitHub, cliquez sur la flèche ↓ en haut à droite pour le télécharger.
📄 [Voir le dashboard Power BI complet](https://github.com/sarra725/diabetes-detection-fullstack/blob/main/03_images/diabetes_powerbi_dashboard.pdf)




# 🩺 Détection du Diabète — Application Web Full-Stack avec Dashboard Power BI

> Projet complet de bout en bout : analyse exploratoire des données, dashboard Power BI interactif, modèle Random Forest exposé via une API Flask, et application web Symfony avec authentification et interface de prédiction médicale.

---

## 🎯 Objectif du projet

Développer une solution complète de détection du risque diabétique permettant à des professionnels de santé de :
- **Analyser** les profils épidémiologiques des patients via un dashboard interactif
- **Prédire** en temps réel le risque diabétique d'un patient à partir de ses données cliniques
- **Recevoir** des recommandations personnalisées selon le niveau de risque

---

## 🗂️ Structure du projet

```
📁 diabetes_project/
│
├── 🐍 app.py                        # API Flask — endpoints de prédiction
├── 🐍 train_model.py                # Entraînement et sauvegarde du modèle
├── 🤖 diabetes_model.pkl            # Modèle Random Forest sérialisé
│
├── 📁 symfony_app/                  # Application web Symfony
│   ├── authentification/            # Login / Logout / Gestion des rôles
│   ├── dashboard/                   # Intégration Power BI
│   └── prediction/                  # Interface de prédiction (appel API Flask)
│
└── 📁 images/
    ├── login.png                    # Page de connexion
    ├── profils_dashboard.png        
    ├── metabolique_dashboard.png    
    ├── geographie_dashboard.png     
    ├── prediction_form.png          
    └── user_profile.png             
    └── 📄 diabetes_powerbi_dashboard.pdf 
```

---

## 🏗️ Architecture de l'application

```
┌─────────────────────────────────────────────────────────┐
│              Application Symfony (Frontend)              │
│   Login · Dashboard Power BI · Formulaire Prédiction    │
└──────────────────────┬──────────────────────────────────┘
                       │ HTTP POST /predict
┌──────────────────────▼──────────────────────────────────┐
│              API Flask (Backend ML)                      │
│     Prétraitement · Standardisation · Prédiction        │
└──────────────────────┬──────────────────────────────────┘
                       │ pickle.load()
┌──────────────────────▼──────────────────────────────────┐
│           Modèle Random Forest (.pkl)                    │
│     Accuracy : 99.35% · CV Score : 98.54%               │
└─────────────────────────────────────────────────────────┘
```

---

## 🤖 Modèle ML — Random Forest

📄 [`train_model.py`](train_model.py) · [`app.py`](app.py) · [`diabetes_model.pkl`](diabetes_model.pkl)

### Dataset — Pima Indians Diabetes
768 patients (500 non-diabétiques + 268 diabétiques) avec les features suivantes :

| Feature | Description | Plage |
|---|---|---|
| `pregnancies` | Nombre de grossesses | 0 – 17 |
| `glucose` | Glycémie (mg/dL) | 50 – 250 |
| `blood_pressure` | Pression artérielle (mmHg) | 40 – 130 |
| `skin_thickness` | Épaisseur du pli cutané (mm) | 0 – 60 |
| `insulin` | Insuline (mu U/ml) | 0 – 500 |
| `bmi` | Indice de masse corporelle | 15 – 60 |
| `diabetes_pedigree` | Antécédents familiaux | 0.05 – 2.5 |
| `age` | Âge du patient | 18 – 81 |

### Performances du modèle

| Métrique | Score |
|:---|:---:|
| Accuracy (test set) | **99.35%** |
| Cross-validation (5-fold) | **98.54%** |

### Paramètres Random Forest

```python
RandomForestClassifier(
    n_estimators=200,
    max_depth=8,
    min_samples_split=5,
    min_samples_leaf=2,
    class_weight="balanced",
    random_state=42
)
```

> **Note :** `class_weight="balanced"` utilisé pour compenser le déséquilibre entre classes (500 vs 268).

---

## 🌐 API Flask

### Endpoints

| Méthode | Route | Description |
|---|---|---|
| `GET` | `/` | Vérification de l'état de l'API |
| `GET` | `/health` | Status + version du modèle |
| `POST` | `/predict` | Prédiction du risque diabétique |

### Exemple de requête

```json
POST /predict
{
  "pregnancies": 2,
  "glucose": 120,
  "blood_pressure": 70,
  "skin_thickness": 88,
  "insulin": 100,
  "bmi": 50,
  "diabetes_pedigree": 2.5,
  "age": 50
}
```

### Exemple de réponse

```json
{
  "prediction": 1,
  "is_diabetic": true,
  "probability_diabetic": 85.71,
  "probability_healthy": 14.29,
  "risk_level": "Élevé",
  "risk_color": "danger",
  "message": "Diabétique",
  "recommendations": [
    "⚠️ IMC élevé (50.0) — Consultez un nutritionniste.",
    "🏥 Risque élevé — Une consultation médicale urgente est fortement recommandée."
  ]
}
```

### Niveaux de risque

| Probabilité | Niveau | Couleur |
|:---:|:---:|:---:|
| ≥ 70% | Élevé | 🔴 danger |
| 40% – 69% | Modéré | 🟡 warning |
| < 40% | Faible | 🟢 success |

---

## 🖥️ Application Symfony

### Fonctionnalités

**🔐 Authentification**
- Login / Logout sécurisé
- Gestion des rôles (Administrateur, Utilisateur)
- Page profil utilisateur modifiable

**📊 Dashboard Power BI intégré**
Trois pages analytiques interactives avec filtres dynamiques (diabète, genre, pays, âge, année) :

**📋 Formulaire de Prédiction**
- Saisie des 8 paramètres cliniques du patient
- Appel en temps réel à l'API Flask
- Affichage du résultat, probabilité et recommandations personnalisées

---

## 📊 Dashboards Power BI

### KPIs & Questions analytiques

**Page Profils**
- Total des patients : **90K**
- Répartition par genre : Femmes **58,12%** — Hommes **41,86%**
- Distribution géographique par pays
- Répartition des diabétiques par tranche d'âge

**Page Métabolique**
- BMI maximum : **50** · Moyenne BMI : **27**
- Glycémie maximale : **240 mg/dL** · Glycémie moyenne : **135 mg/dL**
- Évolution du BMI moyen par âge
- Nombre de diabétiques selon l'historique tabagique
- Glycémie moyenne par tranche d'âge et selon l'IMC

> **Note :** Les variables `glucose`, `bmi`, `age` et `insulin` utilisées dans le modèle Flask sont également les principaux indicateurs observés dans les dashboards Power BI, confirmant leur pertinence en tant que **KPIs cliniques** clés pour la détection du diabète.

**Page Géographie**
- Répartition des diabétiques par pays (carte interactive)
- Variation annuelle du nombre de diabétiques par pays (1980–2015)
- Répartition par sexe par pays : Femmes **49,66%** — Hommes **50,34%**

### Captures d'écran

| Dashboard | Aperçu |
|---|---|
| **Profils** | ![Dashboard Profils](images/profils_dashboard.png) |
| **Métabolique** | ![Dashboard Métabolique](images/metabolique_dashboard.png) |
| **Géographie** | ![Dashboard Géographie](images/geographie_dashboard.png) |

---

## 🖼️ Aperçu de l'Application

| Page | Aperçu |
|---|---|
| **Connexion** | ![Page de connexion](images/login.png) |
| **Prédiction** | ![Interface de prédiction](images/prediction_form.png) |
| **Profil utilisateur** | ![Profil](images/user_profile.png) |

---

## 🚀 Installation & Utilisation

### Prérequis

```bash
# API Flask
pip install flask flask-cors numpy scikit-learn

# Application Symfony
composer install
```

### Lancer l'API Flask

```bash
python app.py
# → API disponible sur http://localhost:5000
```

### Lancer l'application Symfony

```bash
symfony serve
# → Application disponible sur http://localhost:8000
```

> Le modèle `diabetes_model.pkl` est chargé automatiquement au démarrage de Flask. S'il n'existe pas, `train_model.py` est exécuté automatiquement.

---

## 📦 Dépendances

| Technologie | Usage |
|---|---|
| **Symfony** | Framework PHP — Application web, authentification, routing |
| **Flask** | API REST Python — Exposition du modèle ML |
| **scikit-learn** | Entraînement du Random Forest, StandardScaler |
| **Power BI** | Dashboards analytiques interactifs |
| **pickle** | Sérialisation du modèle entraîné |
| **flask-cors** | Gestion des requêtes cross-origin Symfony → Flask |

---

## 👩‍💻 Auteure

**Sarra** — Étudiante en Machine Learning & Développement Web  
Projet réalisé dans le cadre d'un cours de data science appliqué au domaine médical.

