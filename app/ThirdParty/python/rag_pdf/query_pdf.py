import sys
import json
from pdf_processor import load_and_split_pdf
from vector_store import create_or_load_vectorstore
from rag_chain import setup_qa_chain

def main():
    if len(sys.argv) < 3:
        print("ERROR: Missing arguments. Usage: python query_pdf.py <pdf_path> <question>")
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    question = sys.argv[2]
    
    try:
        # Proses PDF
        docs = load_and_split_pdf(pdf_path)
        vectorstore = create_or_load_vectorstore(docs)
        qa_chain = setup_qa_chain(vectorstore)
        
        # Eksekusi query
        result = qa_chain({"query": question})
        
        # Format output
        sources = []
        for doc in result["source_documents"]:
            sources.append({
                "content": doc.page_content,
                "page": doc.metadata.get("page", "N/A"),
                "source": doc.metadata.get("source", "N/A")
            })
        
        output = {
            "answer": result["result"],
            "sources": sources
        }
        
        print(json.dumps(output))
        
    except Exception as e:
        print(f"ERROR: {str(e)}")
        sys.exit(1)

if __name__ == "__main__":
    main()