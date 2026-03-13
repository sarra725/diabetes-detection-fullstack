# -*- coding: utf-8 -*-
from flask import Flask, request, jsonify
from flask_cors import CORS
import numpy as np
import pickle
import os
from train_model import train_and_save_model

app = Flask(__name__)
CORS(app)

MODEL_PATH = "diabetes_model.pkl"

# Train model if not exists
if not os.path.exists(MODEL_PATH):
    print("Training model...")
    train_and_save_model()

with open(MODEL_PATH, "rb") as f:
    model_data = pickle.load(f)
    model = model_data["model"]
    scaler = model_data["scaler"]
    feature_names = model_data["feature_names"]


@app.route("/", methods=["GET"])
def index():
    return "API Flask fonctionne ✅", 200


@app.route("/health", methods=["GET"])
def health():
    return jsonify({"status": "ok", "model": "DiabetesPredictor v1.0"})


@app.route("/predict", methods=["POST"])
def predict():
    try:
        data = request.get_json()
        if not data:
            return jsonify({"error": "No data provided"}), 400

        required = ["pregnancies", "glucose", "blood_pressure", "skin_thickness",
                    "insulin", "bmi", "diabetes_pedigree", "age"]

        missing = [f for f in required if f not in data]
        if missing:
            return jsonify({"error": f"Missing fields: {', '.join(missing)}"}), 400

        features = np.array([[
            float(data["pregnancies"]),
            float(data["glucose"]),
            float(data["blood_pressure"]),
            float(data["skin_thickness"]),
            float(data["insulin"]),
            float(data["bmi"]),
            float(data["diabetes_pedigree"]),
            float(data["age"])
        ]])

        features_scaled = scaler.transform(features)
        prediction = model.predict(features_scaled)[0]
        probability = model.predict_proba(features_scaled)[0]

        prob_diabetic = float(probability[1])
        prob_healthy  = float(probability[0])

        if prob_diabetic >= 0.7:
            risk_level = "Élevé"
            risk_color = "danger"
        elif prob_diabetic >= 0.4:
            risk_level = "Modéré"
            risk_color = "warning"
        else:
            risk_level = "Faible"
            risk_color = "success"

        recommendations = get_recommendations(data, prob_diabetic)

        return jsonify({
            "prediction":            int(prediction),
            "is_diabetic":           bool(prediction == 1),
            "probability_diabetic":  round(prob_diabetic * 100, 2),
            "probability_healthy":   round(prob_healthy  * 100, 2),
            "risk_level":            risk_level,
            "risk_color":            risk_color,
            "recommendations":       recommendations,
            "message":               "Diabétique" if prediction == 1 else "Non Diabétique"
        })

    except ValueError as e:
        return jsonify({"error": f"Invalid value: {str(e)}"}), 400
    except Exception as e:
        return jsonify({"error": str(e)}), 500


def get_recommendations(data, prob):
    recs = []
    bmi     = float(data.get("bmi",     0))
    glucose = float(data.get("glucose", 0))
    age     = float(data.get("age",     0))

    if bmi > 30:
        recs.append("⚠️ IMC élevé ({:.1f}) — Consultez un nutritionniste et pratiquez une activité physique régulière.".format(bmi))
    elif bmi > 25:
        recs.append("📊 IMC légèrement élevé ({:.1f}) — Surveillez votre alimentation.".format(bmi))

    if glucose > 140:
        recs.append("🩸 Glycémie élevée ({} mg/dL) — Réduisez les sucres raffinés et consultez un médecin.".format(glucose))
    elif glucose > 100:
        recs.append("🩸 Glycémie limite ({} mg/dL) — Adoptez une alimentation équilibrée.".format(glucose))

    if age > 45:
        recs.append("👴 Âge à risque — Effectuez des bilans glycémiques réguliers (tous les 6 mois).")

    if prob >= 0.7:
        recs.append("🏥 Risque élevé — Une consultation médicale urgente est fortement recommandée.")
    elif prob >= 0.4:
        recs.append("📋 Risque modéré — Consultez votre médecin traitant pour un bilan complet.")
    else:
        recs.append("✅ Continuez vos bonnes habitudes de vie et effectuez des contrôles annuels.")

    return recs


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=False)
