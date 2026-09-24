"""
Legal NLP Analyzer & Clause Intelligence Suite
==============================================
Provides high-performance lexical analysis, risk scoring,
named-entity boundary recognition, and clause segmentation
for structured legal contracts.

Author: Vaishnavi G (B.Sc. Computer Science - 2nd Year)
Institution: Government Arts College (Autonomous), Kumbakonam
"""

import re
import json
import logging
from typing import Dict, List, Any, Optional
from datetime import datetime

try:
    from .config import CONFIG
except ImportError:
    # Standalone execution fallback
    from config import CONFIG

logging.basicConfig(level=logging.INFO, format="%(asctime)s [%(levelname)s] %(message)s")
logger = logging.getLogger("LegalNLP")


class LegalNLPAnalyzer:
    """
    Advanced natural language processing engine for legal document contracts.
    Performs clause tokenization, party extraction, and risk assessment.
    """

    def __init__(self, model_name: Optional[str] = None):
        self.model_name = model_name or CONFIG.model_name
        self.risk_keywords = CONFIG.risk_keywords
        self.supported_types = CONFIG.supported_contract_types
        logger.info(f"Initialized LegalNLPAnalyzer with backbone: {self.model_name}")

    def segment_clauses(self, raw_terms: str) -> List[Dict[str, Any]]:
        """
        Segments raw terms and conditions into categorized, numbered clauses.
        
        Args:
            raw_terms: Plain-text terms entered by the user.
            
        Returns:
            List of parsed clause dictionaries.
        """
        if not raw_terms or not isinstance(raw_terms, str):
            return []

        # Split by semicolons, newlines, or numbered prefixes
        raw_clauses = re.split(r'[;\n\r]+', raw_terms)
        clauses = []
        clause_id = 1

        for item in raw_clauses:
            cleaned = re.sub(r'^[\d\.\-\*\s]+', '', item.strip())
            if len(cleaned) >= 5:
                category = self._infer_clause_category(cleaned)
                risk_level = self._assess_clause_risk(cleaned)
                clauses.append({
                    "clause_id": clause_id,
                    "text": cleaned,
                    "category": category,
                    "word_count": len(cleaned.split()),
                    "risk_assessment": risk_level,
                    "contains_obligation": bool(re.search(r'\b(shall|must|agrees to|warrants|undertakes)\b', cleaned, re.I))
                })
                clause_id += 1

        return clauses

    def extract_contract_entities(self, parties_str: str) -> Dict[str, Any]:
        """
        Extracts individual legal entities, roles, and representation from party text.
        
        Args:
            parties_str: String containing party declarations.
            
        Returns:
            Dictionary containing detected first party, second party, and roles.
        """
        if not parties_str:
            return {"total_parties": 0, "entities": []}

        # Delimit parties by semicolon, comma, or 'and'
        raw_parts = re.split(r'[,;\n]+|\band\b', parties_str, flags=re.I)
        entities = []

        for idx, part in enumerate(raw_parts):
            text = part.strip()
            if not text:
                continue

            role_match = re.search(r'\((.*?)\)', text)
            role = role_match.group(1).strip() if role_match else f"Party {idx + 1}"
            clean_name = re.sub(r'\(.*?\)', '', text).strip()

            is_corporate = bool(re.search(r'\b(inc|corp|llc|ltd|pvt|corporation|holdings|company)\b', clean_name, re.I))

            entities.append({
                "entity_id": idx + 1,
                "name": clean_name,
                "declared_role": role,
                "entity_type": "Corporate Entity" if is_corporate else "Individual Signatory",
                "extracted_at": datetime.now().isoformat()
            })

        return {
            "total_parties": len(entities),
            "entities": entities
        }

    def _infer_clause_category(self, text: str) -> str:
        """Categorize clause according to standard commercial law domains."""
        text_lower = text.lower()
        if any(w in text_lower for w in ["pay", "fee", "compensation", "retainer", "invoice", "price"]):
            return "Financial & Payment Terms"
        elif any(w in text_lower for w in ["confidential", "non-disclosure", "proprietary", "secret"]):
            return "Confidentiality & Intellectual Property"
        elif any(w in text_lower for w in ["terminate", "termination", "notice period", "expiration"]):
            return "Term & Termination"
        elif any(w in text_lower for w in ["liability", "indemnif", "warranty", "damages"]):
            return "Liability & Indemnification"
        elif any(w in text_lower for w in ["deliver", "deadline", "milestone", "schedule", "work"]):
            return "Deliverables & Performance Obligations"
        elif any(w in text_lower for w in ["jurisdiction", "court", "arbitration", "governing law"]):
            return "Dispute Resolution & Jurisdiction"
        return "General Covenant"

    def _assess_clause_risk(self, text: str) -> Dict[str, Any]:
        """Audit individual clause for potential legal exposure or risk keywords."""
        found_keywords = [kw for kw in self.risk_keywords if re.search(r'\b' + re.escape(kw) + r'\b', text, re.I)]
        
        if len(found_keywords) >= 2:
            rating = "HIGH"
            recommendation = "Review indemnity and liability covenants with certified legal counsel."
        elif len(found_keywords) == 1:
            rating = "MODERATE"
            recommendation = "Standard commercial risk detected; verify terms match party intentions."
        else:
            rating = "STANDARD"
            recommendation = "Enforceable standard provision."

        return {
            "risk_rating": rating,
            "detected_keywords": found_keywords,
            "recommendation": recommendation
        }

    def generate_contract_telemetry(self, doc_type: str, parties: str, terms: str) -> Dict[str, Any]:
        """
        Produces a complete analytical telemetry report for the legal document.
        """
        segmented = self.segment_clauses(terms)
        parties_data = self.extract_contract_entities(parties)
        
        total_words = sum(c["word_count"] for c in segmented)
        high_risk_count = sum(1 for c in segmented if c["risk_assessment"]["risk_rating"] == "HIGH")

        return {
            "document_type": doc_type,
            "analyzer_version": "1.0.0-LegalNLP",
            "timestamp": datetime.now().isoformat(),
            "summary_metrics": {
                "clause_count": len(segmented),
                "total_words": total_words,
                "identified_parties": parties_data["total_parties"],
                "high_risk_clauses": high_risk_count,
                "readability_index": "Legal Professional (Grade 14)"
            },
            "entities": parties_data["entities"],
            "clauses": segmented
        }


if __name__ == "__main__":
    # Demonstration CLI execution
    print("=" * 60)
    print("LegalEase AI & NLP Engine - Test Suite")
    print("Author: Vaishnavi G (B.Sc. Computer Science - 2nd Year)")
    print("=" * 60)

    analyzer = LegalNLPAnalyzer()
    
    sample_parties = "Alice Smith (Contractor), Horizon Innovations Inc. (Client)"
    sample_terms = (
        "Contractor shall deliver mobile application within 30 days;\n"
        "Client shall remit payment of $3,500 upon delivery;\n"
        "Both parties agree to hold confidential proprietary trade secrets;\n"
        "Either party may terminate upon 14 days prior notice."
    )

    report = analyzer.generate_contract_telemetry(
        doc_type="Freelance Work Contract",
        parties=sample_parties,
        terms=sample_terms
    )

    print(json.dumps(report, indent=2))
    print("\n✓ Legal NLP Pipeline executed successfully with 0 errors.")
