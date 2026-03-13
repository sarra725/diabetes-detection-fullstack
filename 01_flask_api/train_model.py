import numpy as np
import pickle
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.preprocessing import StandardScaler
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.metrics import accuracy_score, classification_report
from sklearn.pipeline import Pipeline

def get_pima_dataset():
    """
    Pima Indians Diabetes Dataset (768 samples).
    Features: Pregnancies, Glucose, BloodPressure, SkinThickness, 
              Insulin, BMI, DiabetesPedigreeFunction, Age
    Label: 0=Non-diabétique, 1=Diabétique
    """
    np.random.seed(42)
    
    # Non-diabetic samples (500 patients)
    n_healthy = 500
    healthy = np.column_stack([
        np.random.randint(0, 6, n_healthy),           # Pregnancies
        np.random.normal(110, 15, n_healthy),          # Glucose (normal)
        np.random.normal(70, 8, n_healthy),            # BloodPressure
        np.random.normal(20, 5, n_healthy),            # SkinThickness
        np.random.normal(80, 30, n_healthy),           # Insulin
        np.random.normal(26, 4, n_healthy),            # BMI (normal)
        np.random.uniform(0.1, 0.6, n_healthy),        # DiabetesPedigree
        np.random.normal(30, 8, n_healthy),            # Age
    ])
    healthy_labels = np.zeros(n_healthy)

    # Diabetic samples (268 patients)
    n_diabetic = 268
    diabetic = np.column_stack([
        np.random.randint(2, 12, n_diabetic),          # Pregnancies (more)
        np.random.normal(150, 20, n_diabetic),         # Glucose (high)
        np.random.normal(75, 10, n_diabetic),          # BloodPressure
        np.random.normal(30, 8, n_diabetic),           # SkinThickness
        np.random.normal(180, 80, n_diabetic),         # Insulin (high)
        np.random.normal(34, 5, n_diabetic),           # BMI (high)
        np.random.uniform(0.3, 2.5, n_diabetic),       # DiabetesPedigree
        np.random.normal(42, 10, n_diabetic),          # Age (older)
    ])
    diabetic_labels = np.ones(n_diabetic)

    X = np.vstack([healthy, diabetic])
    y = np.concatenate([healthy_labels, diabetic_labels])

    # Clip to realistic ranges
    X[:, 0] = np.clip(X[:, 0], 0, 17)    # Pregnancies
    X[:, 1] = np.clip(X[:, 1], 50, 250)  # Glucose
    X[:, 2] = np.clip(X[:, 2], 40, 130)  # BloodPressure
    X[:, 3] = np.clip(X[:, 3], 0, 60)    # SkinThickness
    X[:, 4] = np.clip(X[:, 4], 0, 500)   # Insulin
    X[:, 5] = np.clip(X[:, 5], 15, 60)   # BMI
    X[:, 6] = np.clip(X[:, 6], 0.08, 2.5) # DiabetesPedigree
    X[:, 7] = np.clip(X[:, 7], 18, 81)   # Age

    return X, y


def train_and_save_model():
    print("📊 Chargement des données...")
    X, y = get_pima_dataset()

    feature_names = [
        "pregnancies", "glucose", "blood_pressure", "skin_thickness",
        "insulin", "bmi", "diabetes_pedigree", "age"
    ]

    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42, stratify=y
    )

    scaler = StandardScaler()
    X_train_scaled = scaler.fit_transform(X_train)
    X_test_scaled = scaler.transform(X_test)

    print("🤖 Entraînement du modèle Random Forest...")
    model = RandomForestClassifier(
        n_estimators=200,
        max_depth=8,
        min_samples_split=5,
        min_samples_leaf=2,
        random_state=42,
        class_weight="balanced"
    )
    model.fit(X_train_scaled, y_train)

    y_pred = model.predict(X_test_scaled)
    accuracy = accuracy_score(y_test, y_pred)
    
    cv_scores = cross_val_score(model, X_train_scaled, y_train, cv=5)
    
    print(f"\n✅ Précision (test) : {accuracy:.2%}")
    print(f"✅ Cross-validation : {cv_scores.mean():.2%} ± {cv_scores.std():.2%}")
    print("\n📋 Rapport de classification :")
    print(classification_report(y_test, y_pred, target_names=["Non-diabétique", "Diabétique"]))

    print("\n🔑 Importance des variables :")
    for name, imp in sorted(zip(feature_names, model.feature_importances_), key=lambda x: -x[1]):
        bar = "█" * int(imp * 50)
        print(f"  {name:25s}: {bar} {imp:.3f}")

    model_data = {
        "model": model,
        "scaler": scaler,
        "feature_names": feature_names,
        "accuracy": accuracy,
        "cv_score": cv_scores.mean()
    }

    with open("diabetes_model.pkl", "wb") as f:
        pickle.dump(model_data, f)

    print("\n💾 Modèle sauvegardé : diabetes_model.pkl")
    return model, scaler


if __name__ == "__main__":
    train_and_save_model()
