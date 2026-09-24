# LegalEase AI & NLP Engine (Python Suite)

## Academic Research & Clause Intelligence Module
* **Author:** Vaishnavi G
* **Academic Year:** 2nd Year (B.Sc. Computer Science)
* **Institution:** Government Arts College (Autonomous), Kumbakonam

---

### Overview
This directory contains the **LegalEase Python NLP & Machine Learning Suite**, designed as an offline research and clause intelligence component for legal contract preprocessing, tokenization, risk scoring, and entity boundary extraction.

### Architecture Components
1. **`legal_nlp_analyzer.py`**:
   - Automated clause segmentation and classification into commercial legal domains.
   - Party entity role identification (Individual Signatory vs Corporate Entity).
   - High-risk liability and indemnity covenant detection.
2. **`dataset_preprocessor.py`**:
   - Ingests verified legal schemas from `data/templates.json`.
   - Generates vocabulary distributions and lexical token statistics.
3. **`train_contract_model.py`**:
   - Neural fine-tuning simulation for domain-adapted legal contract categorization.
4. **`config.py`**:
   - Centralized hyperparameters, sequence lengths, and risk keyword definitions.

### Quick Start / CLI Execution
```bash
# Optional virtual environment setup
cd ai_engine
python -m legal_nlp_analyzer
python -m train_contract_model
```
*(Note: The main LegalEase web application runs server-side on PHP & Vanilla JS. This Python module serves as an advanced NLP research companion).*
