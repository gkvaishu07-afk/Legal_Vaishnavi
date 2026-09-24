"""
AI Engine Configuration Settings & Hyperparameters
==================================================
Stores model architecture parameters, tokenization limits,
and preprocessing constants for the Legal NLP Engine.
"""

import os
from dataclasses import dataclass, field
from typing import List


@dataclass
class NLPModelConfig:
    """Hyperparameter and runtime configuration for Legal NLP models."""
    model_name: str = "legal-bert-base-uncased"
    max_sequence_length: int = 512
    batch_size: int = 16
    learning_rate: float = 2e-5
    num_train_epochs: int = 4
    warmup_ratio: float = 0.1
    weight_decay: float = 0.01
    
    # Contract Classifications Supported
    supported_contract_types: List[str] = field(default_factory=lambda: [
        "Freelance Work Contract",
        "Non-Disclosure Agreement (NDA)",
        "Service Agreement",
        "Business Agreement",
        "Lease Agreement",
        "Employment Offer Letter",
        "Partnership Agreement",
        "General Contract"
    ])

    # Severity Risk Flags for Clause Auditing
    risk_keywords: List[str] = field(default_factory=lambda: [
        "indemnify", "unlimited liability", "exclusive jurisdiction",
        "non-compete", "perpetual license", "liquidated damages",
        "unilateral termination", "irrevocable transfer"
    ])


# Global Default Configuration Instance
CONFIG = NLPModelConfig()
