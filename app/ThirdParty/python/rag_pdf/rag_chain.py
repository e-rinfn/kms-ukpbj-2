from langchain.chains import RetrievalQA
from langchain.llms import Ollama

def setup_qa_chain(vectorstore):
    """Menyiapkan RAG chain"""
    llm = Ollama(model="llama2")
    
    return RetrievalQA.from_chain_type(
        llm=llm,
        chain_type="stuff",
        retriever=vectorstore.as_retriever(),
        return_source_documents=True
    )