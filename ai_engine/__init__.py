"""
LegalEase AI & NLP Engine
=========================
Advanced Legal Natural Language Processing & Automated Clause Extraction Suite.
Conceived and Developed by Vaishnavi G (B.Sc. Computer Science - 2nd Year).
"""

__version__ = "1.0.0"
__author__ = "Vaishnavi G"
__institution__ = "Government Arts College (Autonomous), Kumbakonam"
__license__ = "Academic Research License"

from .legal_nlp_analyzer import LegalNLPAnalyzer
from .dataset_preprocessor import LegalDatasetPreprocessor

__all__ = [
    "LegalNLPAnalyzer",
    "LegalDatasetPreprocessor",
]
