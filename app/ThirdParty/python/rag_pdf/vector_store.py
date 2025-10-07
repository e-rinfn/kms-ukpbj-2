from langchain.vectorstores import Chroma
from langchain.embeddings import OllamaEmbeddings
import os

def create_or_load_vectorstore(docs, persist_directory="db"):
    """Membuat atau memuat vector store dari dokumen"""
    embeddings = OllamaEmbeddings(model="nomic-embed-text")
    
    return Chroma.from_documents(
        documents=docs,
        embedding=embeddings,
        persist_directory=persist_directory
    )