"""
Legal Dataset Preprocessor & Feature Transformer
================================================
Processes structured contract templates and builds feature
tensors / vocabulary sets for downstream legal classification models.

Author: Vaishnavi G (B.Sc. Computer Science - 2nd Year)
Institution: Government Arts College (Autonomous), Kumbakonam
"""

import os
import json
import logging
from typing import Dict, List, Any

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s")
logger = logging.getLogger("DatasetPreprocessor")


class LegalDatasetPreprocessor:
    """Utility to load, clean, and vectorize legal agreements from templates.json."""

    def __init__(self, templates_path: str = None):
        if templates_path is None:
            # Resolves path relative to NMvaishnavi root
            base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
            templates_path = os.path.join(base_dir, "data", "templates.json")
        
        self.templates_path = templates_path
        self.dataset = self.load_templates()

    def load_templates(self) -> List[Dict[str, Any]]:
        """Load contract schemas from data/templates.json."""
        if not os.path.exists(self.templates_path):
            logger.warning(f"Template file not found at: {self.templates_path}. Using fallback cache.")
            return []

        try:
            with open(self.templates_path, "r", encoding="utf-8") as f:
                data = json.load(f)
                templates = data.get("templates", [])
                logger.info(f"Loaded {len(templates)} master legal contract templates.")
                return templates
        except Exception as e:
            logger.error(f"Error loading templates: {e}")
            return []

    def build_vocabulary(self) -> Dict[str, int]:
        """Construct token frequency dictionary across contract recitals and sections."""
        vocab: Dict[str, int] = {}
        for item in self.dataset:
            text_corpus = " ".join([
                item.get("name", ""),
                item.get("description", ""),
                " ".join(item.get("recitals", [])),
                " ".join(item.get("sections", []))
            ]).lower()

            words = [w.strip(".,;:\"'()[]{}") for w in text_corpus.split() if len(w) > 2]
            for w in words:
                vocab[w] = vocab.get(w, 0) + 1

        return dict(sorted(vocab.items(), key=lambda x: x[1], reverse=True))

    def export_token_statistics(self) -> Dict[str, Any]:
        """Generate statistical summary of dataset tokens."""
        vocab = self.build_vocabulary()
        total_tokens = sum(vocab.values())
        unique_tokens = len(vocab)
        top_legal_terms = list(vocab.keys())[:15]

        return {
            "template_count": len(self.dataset),
            "total_tokens": total_tokens,
            "unique_vocabulary_size": unique_tokens,
            "most_frequent_terms": top_legal_terms
        }


if __name__ == "__main__":
    preprocessor = LegalDatasetPreprocessor()
    stats = preprocessor.export_token_statistics()
    print("\n--- LegalEase NLP Corpus Preprocessing Statistics ---")
    print(json.dumps(stats, indent=2))
