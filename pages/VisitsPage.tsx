import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, StyleSheet, ActivityIndicator } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';


// Ajouter une fonction pour décoder le JWT sans jwtdecode
function decodeJWT(token: string) {
  const base64Url = token.split('.')[1];
  const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  const jsonPayload = decodeURIComponent(
    atob(base64)
      .split('')
      .map(c => '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2))
      .join('')
  );
  return JSON.parse(jsonPayload);
}

export default function VisitsPage() {
  interface Visit {
    id_visite: number;
    nom_medecin: string;
    prenom_medecin: string;
    date_visite: string;
    rue_cabinet: string;
    ville_cabinet: string;
    code_postal_cabinet: string;
    heure_rdv: string;
  }

  const [visits, setVisits] = useState<Visit[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchVisits = async () => {
      try {
        const token = await AsyncStorage.getItem('authToken');
        const decodedToken = decodeJWT(token || '');
        const visitorId = decodedToken.userId; // Utiliser id_visiteur extrait du token
        const response = await fetch(`https://s5-4242.nuage-peda.fr/gsbapieliot/API/ApiVisite.php?id=${visitorId}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        const data = await response.json();
        setVisits(data);
      } catch (error) {
        console.error('Erreur lors de la récupération des visites:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchVisits();
  }, []);

  if (loading) {
    return (
      <View style={styles.container}>
        <ActivityIndicator size="large" color="#0000ff" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Liste de mes visites</Text>
      <FlatList
        data={visits}
        keyExtractor={(item) => item.id_visite.toString()}
        renderItem={({ item }) => (
          <View style={styles.visitItem}>
            <Text>Médecin: {item.nom_medecin} {item.prenom_medecin}</Text>
            <Text>Date: {item.date_visite}</Text>
            <Text>Adresse: {item.rue_cabinet}, {item.ville_cabinet}, {item.code_postal_cabinet}</Text>
            <Text>Heure du RDV: {item.heure_rdv}</Text>
          </View>
        )}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    padding: 16,
    backgroundColor: '#fff',
  },
  title: {
    fontSize: 24,
    fontWeight: 'bold',
    marginBottom: 16,
  },
  visitItem: {
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#ccc',
  },
});
